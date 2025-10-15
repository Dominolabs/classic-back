<template>

   <div>
      <div class="form-group">
         <label for="product-restaurant_id" class="control-label">Ресторан</label>
         <select
            v-model="restaurant"
            id="product-restaurant_id"
            class="form-control"
         >
            <option :value="null" disabled>Выберите ресторан ...</option>
            <option v-for="restaurant in restaurants" :key="restaurant.id" :value="restaurant">{{ restaurant.name }}</option>
         </select>

         <div class="help-block"></div>
         <input v-if="restaurant" type="hidden" name="Product[restaurant_id]" :value="restaurant.id">
      </div>

      <div v-if="restaurant" class="form-group">
         <label for="product-category_id" class="control-label">Категория</label>
         <select
            v-model="category"
            id="product-category_id"
            class="form-control"
         >
            <option :value="null" disabled>Выберите категорию ...</option>
            <option v-for="category in restaurant.categories" :value="category.id" :key="category.id">{{ category.name }}</option>
         </select>

         <div class="help-block"></div>
         <input type="hidden" name="ProductToCategory[category_id]" :value="category">
      </div>

   </div>



</template>
<script>
    export default {
        props: {
            restaurants: {
                type: Array,
                default() {
                    return [];
                }
            },
            product: {
                type: Object,
                default() {
                    return {};
                }
            }
        },
        data() {
            return {
                restaurant: null,
                category: null,
            }
        },
        created() {
            if(this.product){
                this.restaurant = this.product.restaurant;

                if(this.product.category_id){
                    let selected_cat = this.restaurant.categories.find(item => item.id === this.product.category_id)
                    if(selected_cat){
                        this.category = selected_cat.id;
                    }
                }
            }
        }
    }
</script>
<style scoped>

</style>
