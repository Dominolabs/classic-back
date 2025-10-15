<template>
    <div class="table-responsive">
        <table id="attributes" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th class="text-left"><strong>Значение</strong></th>
                <th class="text-left"><strong>Порядок сортировки</strong></th>
                <th class="action-column"/>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(property, index) in propertiesData">
                <td style="vertical-align: middle; width: 45%;">
                    <div class="input-group" v-for="(language, index2) in languages" v-bind:key="language.language_id">
                        <span class="input-group-addon">
                            <img :src="language.icon" :title="language.name">
                        </span>
                        <input class="form-control" type="text" v-model="property.property[language.code]" placeholder="Характеристика" />
                    </div>
                </td>
                <td style="vertical-align: middle; width: 45%;">
                    <input class="form-control" type="number" min="0" step="1" v-model="property.sort_order" placeholder="Порядок сортировки" />
                </td>
                <td class="text-right">
                    <button type="button" @click="onRemoveProperty(property)" data-toggle="tooltip" title="Удалить" class="btn btn-danger">
                        <i class="fa fa-minus-circle"/>
                    </button>
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"/>
                <td style="vertical-align: middle;" class="text-right">
                    <button type="button" @click="onAddProperty" class="btn btn-primary" data-toggle="tooltip" title="Добавить" data-pjax="0">
                        <i class="fa fa-plus-circle"/>
                    </button>
                </td>
            </tr>
            </tfoot>
        </table>
        <input type="hidden" :name="model_name + '[properties]'" :value="JSON.stringify(propertiesData)">
    </div>
</template>
<script>
    export default {
        props: {
            properties: {
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
                    return 'Product';
                }
            }
        },
        data() {
            return {
                propertiesData: [],
                count: 1,
            }
        },
        methods: {
            onAddProperty() {
                let property = {};
                let count = this.getCount();
                
                this.languages.forEach(language => property[language.code] = '');

                this.propertiesData.push({
                    id: count,
                    property: property,
                    sort_order: count
                });
            },
            onRemoveProperty(property) {
                this.propertiesData.splice(this.propertiesData.indexOf(property), 1);
            },
            getCount() {
                return this.count++
            },
        },
        created() {
            this.propertiesData = this.properties || [];
            console.log(this.model_name);
        }
    }
</script>
