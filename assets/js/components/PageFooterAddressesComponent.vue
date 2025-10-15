<template>
   <div class="table-responsive">
      <table id="attributes" class="table table-striped table-bordered table-hover">
         <thead>
         <tr>
            <th class="text-left"><strong>Колонка №</strong></th>
            <th class="text-left"><strong>Данные</strong></th>
            <th class="text-left"><strong>Порядок сортировки</strong></th>
            <th class="action-column"/>
         </tr>
         </thead>
         <tbody>
         <tr v-for="(column, index) in footerColumnsData">
            <td>
               {{ index + 1 }}
            </td>
            <td style="vertical-align: middle; width: 75%; padding-bottom: 40px">
               <div v-for="(language, index2) in languages" v-bind:key="language.language_id" class="block-container">
                  <label class="component-label">{{ getLabel(language) }}</label>
                  <div class="input-group input_group_flex">
                     <span class="input-group-addon inp_group_addon">
                        <img :src="language.icon" :title="language.name">
                     </span>
                     <textarea class="form-control text_area" type="text" v-model="column.address[language.code]"
                               :placeholder="getPlaceholderText(language, 'address')" cols="10" rows="4"/>
                  </div>
                  <div class="input-group input_group_flex">
                     <span class="input-group-addon inp_group_addon">
                        <img :src="language.icon" :title="language.name">
                     </span>
                     <input class="form-control" type="text" v-model="column.address_links[language.code]"
                            :placeholder="getPlaceholderText(language, 'address_link')"/>
                  </div>
               </div>
               <label class="component-label">Номера телефонов</label>
               <div>
                  <div v-for="(phones, index) in column.phones" v-bind:key="index" class="phone_input_group">
                     <input class="form-control" style="margin-right: 10px" type="text"
                            v-model="column.phones[index]['value']"
                            placeholder="Номер телефона"/>
                     <button type="button" @click="removePhone(column.id, column.phones[index])" data-toggle="tooltip"
                             title="Удалить"
                             class="btn btn-danger">
                        <i class="fa fa-minus-circle"/>
                        <span>Телефон</span>
                     </button>
                  </div>
                  <button type="button" @click="addPhone(column.id)" class="btn btn-primary" data-toggle="tooltip"
                          title="Добавить"
                          data-pjax="0">
                     <i class="fa fa-plus-circle"/>
                     <span>Телефон</span>
                  </button>
               </div>
            </td>
            <td style="vertical-align: top; padding-top: 33px">
               <input class="form-control" type="number" min="0" step="1" v-model="column.sort_order"
                      placeholder="Порядок сортировки"/>
            </td>
            <td class="text-right" style="vertical-align: top; padding-top: 33px">
               <button type="button" @click="removeColumn(column)" data-toggle="tooltip" title="Удалить"
                       class="btn btn-danger">
                  <i class="fa fa-minus-circle"/>
                  <span>Колонку</span>
               </button>
            </td>
         </tr>
         </tbody>
         <tfoot>
         <tr>
            <td colspan="3"/>
            <td style="vertical-align: middle;" class="text-right">
               <button type="button" @click="addColumn" class="btn btn-primary" data-toggle="tooltip" title="Добавить"
                       data-pjax="0">
                  <i class="fa fa-plus-circle"/>
                  <span>Колонку</span>
               </button>
            </td>
         </tr>
         </tfoot>
      </table>
      <input type="hidden" :name="model_name + '[footer_columns]'" :value="JSON.stringify(footerColumnsData)">
   </div>
</template>
<script>
    export default {
        props: {
            footer_columns: {
                type: Array,
                default() {
                    return [];
                }
            },
            languages: {
                type: Array,
                default() {
                    return [];
                }
            },
            model_name: {
                type: String,
                default() {
                    return 'Page';
                }
            }
        },
        data() {
            return {
                footerColumnsData: [],
                count: 1,
                countPhone: 1,
            }
        },
        methods: {
            addColumn() {
                let column = {};
                let links_column = {};

                this.languages.forEach(language => {
                    column[language.code] = '';
                    links_column[language.code] = '';
                });

                this.footerColumnsData.push({
                    id: this.count,
                    sort_order: this.count,
                    address: column,
                    address_links: links_column,
                    phones: [
                        {
                            id: this.getCount('phone'),
                            value: ''
                        }
                    ]
                });
                this.count++;
                this.countPhone++;
            },
            getLastId(columnId = null, type = 'column') {
                if (type === 'column') {
                    if (this.footerColumnsData.length) {
                        return parseInt(this.footerColumnsData[this.footerColumnsData.length - 1]['id'])
                    }
                    return null;
                } else {
                    if (columnId && this.footerColumnsData.length) {
                        let needle = this.footerColumnsData.find(item => item.id === columnId);
                        return parseInt(needle.phones[needle.phones.length - 1]['id'])
                    }
                    return null;
                }
            },
            removeColumn(column) {
                this.footerColumnsData.splice(this.footerColumnsData.indexOf(column), 1);
                this.count--;
            },
            getCount(type = 'phone') {
                return type === 'phone' ? this.countPhone : this.count
            },
            addPhone(columnId) {
                let nextId = this.getLastId(columnId, 'phone') + 1
                let phone = {
                    id: nextId,
                    value: ''
                }
                this.footerColumnsData.forEach(item => {
                    if (item.id === columnId) {
                        item.phones.push(phone);
                    }
                })
            },
            removePhone(columnId, phone) {
                this.footerColumnsData.forEach(item => {
                    if (item.id === columnId) {
                        item.phones.splice(item.phones.indexOf(phone), 1);
                    }
                })
            },
            getPlaceholderText(language, type) {
                switch (language.code) {
                    case 'en':
                        return type === 'address' ? 'Address' : 'Address link';
                    case 'uk':
                        return type === 'address' ? 'Адреса' : 'Посилання адреси';
                    default:
                        return 'Адреса';
                }
            },
            getLabel(language) {
                switch (language.code) {
                    case 'en':
                        return 'Адрес и ссылка (анлгийская версия сайта)';
                    case 'uk':
                        return 'Адрес и ссылка (украинская версия сайта)';
                    default:
                        return 'Адрес и ссылка (украинская версия сайта)';
                }
            }
        },
        created() {
            this.footerColumnsData = this.footer_columns;
            let lastColumnId = this.getLastId();
            if(lastColumnId){
                this.count = lastColumnId + 1;
            }
            console.log(this.footer_columns);
        }
    }
</script>
<style scoped>
   .input_group_flex {
      display: flex;
      justify-content: flex-start;
   }

   .inp_group_addon {
      display: flex;
      align-items: flex-start;
      min-width: 40px;
   }

   .component-label {
      font-style: italic;
      font-weight: lighter;
   }

   .block-container {
      margin-bottom: 30px;
   }

   .text_area {
      margin: 0;
      display: flex;
      flex: 1;
   }

   .phone_input_group {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
   }
</style>
