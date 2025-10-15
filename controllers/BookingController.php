<?php

namespace app\controllers;

use app\components\exceptions\BookingEmailException;
use app\models\WebcamForm;
use app\module\admin\models\SettingForm;
use app\module\admin\module\booking\models\Booking;
use app\module\admin\module\booking\models\Country;
use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\hotelservice\models\Hotelservice;
use app\module\admin\module\room\models\Room;
use app\module\admin\module\team\models\Team;
use app\module\admin\module\tariff\models\Tariff;
use app\module\admin\models\User;
use app\module\admin\module\gallery\models\AlbumImage;
use app\module\admin\module\gallery\models\Album;
use app\module\admin\module\product\models\Category;
use app\module\admin\models\Banner;
use app\module\admin\models\BannerImage;
use app\module\admin\models\Language;
use app\module\admin\models\Page;
use app\module\admin\models\SeoUrl;
use app\module\admin\module\product\models\Product;
use app\module\admin\module\feedback\models\Feedback;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\cart\BookingCart;

class BookingController extends Controller
{
    private $fields_for_mass_assignment = [
        'name',
        'lastname',
        'surname',
        'birth_date' => [
            'is_date' => true,
            'hyphen' => true
        ],
        'country_id',
        'phone',
        'email',
        'checkin_at' => [
            'is_date' => true,
            'hyphen' => false
        ],
        'departure_at' => [
            'is_date' => true,
            'hyphen' => false
        ],
        'payment_type',
        'comment'
    ];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays booking index page.
     *
     * @return string home page rendering result
     * @throws NotFoundHttpException if page not found
     */
    public function actionIndex()
    {
        $get = Yii::$app->request->get();
        $from = $get['from'];
        $to = $get['to'];

        $bookingPageId = isset(Yii::$app->params['bookingPageId']) ? Yii::$app->params['bookingPageId'] : null;

        if ($bookingPageId === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $languageId = Language::getLanguageIdByCode(Yii::$app->language);
        $defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());

        $page = Page::getByIdAndLanguageId($bookingPageId, $languageId);

        if ($languageId != $defaultLanguageId) {
            $defaultPage = Page::getByIdAndLanguageId($bookingPageId, $defaultLanguageId);
        } else {
            $defaultPage = $page;
        }

        $rooms = [];
        $nights_quantity = null;

        if (!empty($from) && !empty($to)) {
            $rooms = Room::getFreeRooms(['status' => Room::STATUS_ACTIVE, 'from' => $from, 'to' => $to]);

            $fromTime = strtotime(str_replace('/', '-', $from));
            $toTime = strtotime(str_replace('/', '-', $to));

            $session = Yii::$app->session;
            $session->set('booking_period', json_encode(['from' => $fromTime, 'to' => $toTime]));

            $timeDiff = $toTime - $fromTime;

            $daysCount = round($timeDiff / (60 * 60 * 24));
            $nights_quantity = $daysCount;
        }

        return $this->render('index', [
            'page' => $page,
            'defaultPage' => $defaultPage,
            'rooms' => $rooms,
            'nights_quantity' => $nights_quantity,
            'from_time' => !empty($fromTime) ? $fromTime : null,
            'to_time' => !empty($toTime) ? $toTime : null
        ]);
    }

    /**
     * Displays booking one room page.
     *
     * @return string home page rendering result
     * @throws NotFoundHttpException if page not found
     */
    public function actionShow()
    {
        $bookingPageId = isset(Yii::$app->params['bookingPageId']) ? Yii::$app->params['bookingPageId'] : null;

        try {
            $alias = trim(substr(Yii::$app->request->getPathInfo(), strrpos(Yii::$app->request->getPathInfo(), '/')),
                '/');
        } catch (InvalidConfigException $e) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $languageId = Language::getLanguageIdByCode(Yii::$app->language);
        $defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());

        $page = Page::getByIdAndLanguageId($bookingPageId, $languageId);

        if ($languageId != $defaultLanguageId) {
            $defaultPage = Page::getByIdAndLanguageId($bookingPageId, $defaultLanguageId);
        } else {
            $defaultPage = $page;
        }

        $seoUrl = SeoUrl::findOne(['keyword' => $alias, 'language_id' => $languageId]);
        $roomId = (int)substr($seoUrl->query, 8);

        $room = Room::getRoom($roomId);

        return $this->render('show', [
            'page' => $page,
            'defaultPage' => $defaultPage,
            'room' => $room,
        ]);
    }

    /**
     * Displays static page.
     *
     * @return string the rendering result
     * @throws NotFoundHttpException if page not found
     */
    public function actionPage()
    {
        try {
            $alias = trim(substr(Yii::$app->request->getPathInfo(), strrpos(Yii::$app->request->getPathInfo(), '/')),
                '/');
        } catch (InvalidConfigException $e) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $languageId = Language::getLanguageIdByCode(Yii::$app->language);
        $defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());
        $seoUrl = SeoUrl::findOne(['keyword' => $alias, 'language_id' => $languageId]);

        if (!empty($seoUrl)) {
            if ((strncmp('page_id=', $seoUrl->query, 8) === 0)) {
                return $this->renderStaticPage($alias, (int)substr($seoUrl->query, 8), $languageId, $defaultLanguageId);
            } elseif (strncmp('album_id=', $seoUrl->query, 9) === 0) {
                return $this->renderAlbumPage((int)substr($seoUrl->query, 9));
            }
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }


    public function actionAddBookingToCart()
    {
        $cart_content = Yii::$app->request->post('cart');
        $cart_content = json_decode($cart_content, true);

        $cart = new BookingCart();
        $cart->add($cart_content);

        return $this->asJson(['message' => Yii::t('hotel', 'Успішно додано до корзини!')]);
    }


    /**
     * @return \yii\web\Response
     */
    public function actionClearBookingCart()
    {
        $cart = new BookingCart();
        $cart->clear();
        return $this->asJson(['message' => Yii::t('hotel', 'Корзина успішно очищена!')]);
    }


    public function actionOrderingPage()
    {
        $session = Yii::$app->session;
        $rooms_booking_page_id = isset(Yii::$app->params['roomsBookingPageId']) ? Yii::$app->params['roomsBookingPageId'] : null;

        if ($rooms_booking_page_id === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $language_id = Language::getLanguageIdByCode(Yii::$app->language);
        $default_language_id = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());

        $page = Page::getByIdAndLanguageId($rooms_booking_page_id, $language_id);

        if ($language_id != $default_language_id) {
            $default_page = Page::getByIdAndLanguageId($rooms_booking_page_id, $default_language_id);
        } else {
            $default_page = $page;
        }

        $cart = new BookingCart();
        $booking_period = json_decode($session->get('booking_period'), true);
        $countries = Country::getAllCountries();

        return $this->render('ordering', compact('page', 'cart', 'default_page', 'booking_period', 'countries'));
    }


    public function actionCreateBookingOrder()
    {
        try {
            $request = Yii::$app->request->post();
            $currency = Currency::findOne(['currency_id' => 1]);
            $country = null;

            $booking = new Booking();
            $booking->cart = BookingCart::transformForBooking();

            $booking = $this->massAttributesAssignment($this->fields_for_mass_assignment, $request, $booking);

            if (isset($request['phone_code_id'])) {
                $country = Country::findOne(['phone_code' => $request['phone_code_id']]);
                $booking->phone_code_id = $country ? $country->country_id : null;
            }

            $booking->payment_status = Booking::PAYMENT_STATUS_NOT_PAID;
            $booking->total = BookingCart::calculateTotal();
            $booking->language_id = Language::getLanguageIdByCode(Yii::$app->language);
            $booking->currency_id = $currency->currency_id; //TODO:: implement currency switch
            $booking->currency_code = $currency->code;
            $booking->currency_value = $currency->value;
            $booking->status = Booking::STATUS_ORDER_NEW;

            if (!$booking->validate()) {
                return $this->asJson([
                    'status' => 'error',
                    'errors' => $booking->getErrors()
                ]);
            }

            // Extra checkin for dates
            $booking_period_session = json_decode(Yii::$app->session->get('booking_period'), true);
            if($booking->checkin_at !== $booking_period_session['from']){
                return $this->asJson([
                    'status' => 'error',
                    'errors' => [
                        'checkin_at' => 'Please do not cheat with dates :)'
                    ]
                ]);
            }
            if($booking->departure_at !== $booking_period_session['to']){
                return $this->asJson([
                    'status' => 'error',
                    'errors' => [
                        'departure_at' => 'Please do not cheat with dates :)'
                    ]
                ]);
            }
            // !Extra checking for dates

            if ($booking->save(false)) {
                BookingCart::clearAll();
                Yii::$app->session->setFlash('success_booking_order');
                Yii::$app->session->remove('booking_period');

                $result = $booking->sendNewBookingEmailsNotifications($country);
                if (!$result) {
                    throw new BookingEmailException('Sending email failed');
                }

                return $this->asJson([
                    'status' => 'success'
                ]);
            }
            throw new \Exception('Error in saving booking');
        } catch (\Throwable $exception) {
            if(get_class($exception ) === 'BookingEmailException'){
                Yii::error($exception->getMessage(). ' ' . $exception->getFile() . ' ' . $exception-> getLine(), 'booking-mails');
                return $this->asJson(['status' => 'success']);
            } else {
                $this->asJson(['error' => 'Internal server error! Please try again later or contact support team.' . $exception->getMessage() . $exception->getLine()]);
            }
        }
    }


    private function transformDate($date, $with_hyphen = false)
    {
        if ($with_hyphen) {
            $parts = explode('/', $date);
            if (count($parts) === 3) {
                return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }
            return $date;
        } else {
            return strtotime($this->transformDate($date, true));
        }
    }


    /**
     * @param $attributes
     * @param $request
     * @param $model
     * @return mixed
     */
    private function massAttributesAssignment($attributes, $request, &$model)
    {
        foreach ($attributes as $attribute => $value) {
            if (is_array($value)) {
                if (array_key_exists($attribute, $request)) {
                    if (isset($value['is_date'])) {
                        $model->$attribute = (isset($value['hyphen']) && $value['hyphen'] === true)
                            ? $this->transformDate($request[$attribute], true)
                            : $this->transformDate($request[$attribute]);
                    }
                }
            } else {
                if (array_key_exists($value, $request)) {
                    $model->$value = $request[$value];
                }
            }
        }
        return $model;
    }

}
