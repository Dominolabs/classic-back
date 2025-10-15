<?php


namespace app\components\cart;



use app\module\admin\module\room\models\Room;

class BookingCart
{
    protected static $cart;
    protected static $cart_name;
    protected static $session;


    /**
     * Cart constructor.
     */
    public function __construct()
    {
        static::$cart_name = 'booking_cart';
        static::$session = \Yii::$app->session;

        if(self::$session->has(self::$cart_name)){
            static::$cart = json_decode(static::$session->get(static::$cart_name), true);
        } else {
            static::$cart = [
                'rooms' => [],
                'move_in' => null,
                'move_out' => null
            ];
        }
    }

    /**
     * @param $items
     */
    public function add($items)
    {
        if(is_array($items)){
            foreach ($items as $item){
                array_push(static::$cart['rooms'], $item);
            }
        } else {
            array_push(static::$cart['rooms'], $items);
        }

        static::$session->set(static::$cart_name, json_encode(static::$cart));
    }


    /**
     * @return mixed
     */
    public static function getContent()
    {
        return static::$cart['rooms'];
    }


    public function addRoomsDescription()
    {
        if(count(static::$cart['rooms']) > 0){
            foreach(static::$cart['rooms'] as &$room){
                try {
                    $room['info'] = Room::getRoom($room['id']);
                } catch (\Throwable $exception) {
                    continue;
                }
            }
        }
        return $this;
    }


    /**
     * @return array
     */
    public function getContentJson()
    {
        return count(static::$cart['rooms']) > 0 ? json_encode(static::$cart['rooms']) : json_encode([]);
    }


    /**
     * @return int
     */
    public function getRoomsQuantity()
    {
        return count(static::$cart['rooms']);
    }


    /**
     * @return int
     */
    public function getGuestsQuantity()
    {
        $guests = 0;
        if(!empty(static::$cart['rooms'])){
            foreach (static::$cart['rooms'] as $room) {
                $guests += (int)$room['adult'];
                $guests += (int)$room['children'];
            }
        }
        return $guests;
    }


    /**
     * @return int
     */
    public function getNightsQuantity()
    {
        if(!empty(static::$cart['rooms'])){
            return static::$cart['rooms'][0]['days'] ? static::$cart['rooms'][0]['days'] : 0;
        }
        return 0;
    }


    /**
     * @return float|int
     */
    public function getTotalValue()
    {
        $value = 0;
        if(!empty(static::$cart['rooms'])){
            foreach (static::$cart['rooms'] as $room) {
                $value += ((int)$room['pricePerDay'] * (int)$room['days']);
            }
        }
        return $value;
    }


    /**
     * @return float|int
     */
    public static function calculateTotal()
    {
        $self = new static();
        return $self->getTotalValue();
    }


    public function clear()
    {
        static::$cart = [
            'rooms' => [],
        ];
        static::$session->set(static::$cart_name, json_encode(static::$cart));
    }


    public static function clearAll()
    {
        static::$cart = [
            'rooms' => [],
        ];
        static::$session->set(static::$cart_name, json_encode(static::$cart));
    }


    public static function transformForBooking ()
    {
        $cart = (new static())->addRoomsDescription();
        $result = [];

        if(!empty($cart_content = $cart->getContent())) {
            foreach ($cart_content as $item) {
                $position = (object) [
                    'room_id' => $item['id'],
                    'room_name' => $item['info']['name'],
                    'price' => (int) $item['pricePerDay'] * (int) $item['days'],
                    'guests' => (object)[
                        'adults' => (object)[
                            'quantity' => $item['adult']
                        ],
                        'children' => (object)[
                            'quantity' => $item['children']
                        ]
                    ]
                ];

                $result[] = $position;
            }
            return json_encode($result);
        }
        throw new \Exception('Error in evaluating cart content');
    }


    /**
     * @return array
     */
    public function getRoomsAndQuantity()
    {
        $result = [];
        foreach(self::$cart['rooms'] as $item){
            if(isset($result[$item['id']])){
                $result[$item['id']] += 1;
            } else {
                $result[$item['id']] = 1;
            }
        }
        return $result;
    }
}