<template>

   <div class="form-group field-event-description-name-1">

      <div class="form-group field-banner-status">
         <label class="control-label">Таги</label>
         <div v-if="currentTagsData.length">
            <h5 class="subtitle">Выбранные</h5>
            <div class="tags-container">
               <button
                  v-for="(tag, index) in currentTagsData"
                  type="button"
                  class="btn btn-primary tag-btn"
                  data-toggle="tooltip"
                  title="Добавить"
                  data-pjax="0"
                  @click="removeTag(tag)"
               >
                  <span># {{ tag.name }}</span>
                  <i class="fa fa-minus-circle" style="margin-left: 5px"/>
               </button>
            </div>
         </div>
         <div v-else>
            <span class="note">В текущее вермя ни один таг не привязан к новости.</span>
         </div>

         <div>
            <h5 class="subtitle">Доступные</h5>
            <div class="tags-container">
               <button
                  v-for="(tag, index) in tagsData"
                  :key="index"
                  @click="addTag(tag)"
                  type="button"
                  class="btn btn-warning tag-btn"
                  data-toggle="tooltip"
                  title="Добавить"
                  data-pjax="0"
               >
                  <span># {{ tag.name }}</span>
                  <i class="fa fa-plus-circle" style="margin-left: 5px"/>
               </button>
            </div>
         </div>
         <div class="help-block"></div>
      </div>

      <input type="hidden" :name="model_name + '[tags]'" :value="JSON.stringify(currentTagsData)">
   </div>


</template>
<script>
    export default {
        props: {
            tags: {
                type: Array,
                default() {
                    return [];
                }
            },
            current_tags: {
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
                tagsData: [],
                currentTagsData: [],
            }
        },
        methods: {
            addTag(tag) {
                this.currentTagsData.push(tag);
            },
            removeTag(tag) {
                this.currentTagsData.splice(this.currentTagsData.indexOf(tag), 1);
            }
        },
        created() {
            this.tagsData = this.tags;
            this.currentTagsData = this.current_tags;
        }
    }
</script>
<style scoped>
   .tags-container {
      display: flex;
      justify-content: flex-start;
   }
   .tag-btn {
      margin-right: 10px;
      margin-bottom: 10px;
      padding: 2px 6px;
   }
   .subtitle{
      font-weight: lighter;
      text-decoration: underline;
      font-size: 14px;
   }
   .note {
      color: lightslategrey;
      font-style: italic;
   }
</style>
