<script setup lang="ts">
// Interface
interface Props {
  multiple?: boolean;
  items?: object | any;
  selectPlaceHolder?: string;
  itemName?: string;
  itemValue?: string;
  returnObject?: boolean;
}

interface Emit {
  (e: "scrollEvent", value: any): void;
  (e: "searchData", value: any): void;
  (e: "selectItem", value: any): void;
}

// Props
const props = withDefaults(defineProps<Props>(), {
  multiple: true,
  selectPlaceHolder: "",
  itemName: "name",
  itemValue: "id",
  returnObject: false,
});

// Emit
const emit = defineEmits<Emit>();

// Data
const page = ref<number>(1);
const searchTerm = ref<any>("");
const selected = ref<any[]>([]);

// Methods
// const onIntersect = () => {
//     page.value += 1
//     emit('scrollEvent', page.value)
// }
</script>

<template>
  <VSelect
    v-model="selected"
    :items="props.items"
    :item-title="props?.itemName"
    :item-value="props?.itemValue"
    :return-object="props?.returnObject"
    :menu-props="{
      maxHeight: '250',
      maxWidth: '250',
      contentClass: ['hide-append-icon'],
    }"
    :placeholder="props.selectPlaceHolder"
    :multiple="props?.multiple"
    class="select-item"
    @update:model-value="emit('selectItem', selected)"
  >
    <template #prepend-item>
      <VList class="search-item">
        <VListItem>
          <div>
            <VTextField
              v-model="searchTerm"
              placeholder="Search"
              @input="emit('searchData', searchTerm)"
            />
          </div>
        </VListItem>
      </VList>
      <VDivider class="mt-2 divider-position" />
    </template>
  </VSelect>
</template>

<style scoped>
/*.v-menu>.v-overlay__content>.v-list {
    position: relative !important;
}*/

.search-item {
  position: sticky !important;
  top: 0;
  z-index: 1; /* after add padding in search text field manage z index*/
  background: #fff !important;
  padding: 12px 10px !important; /* for search text field */
}
.divider-position {
  position: sticky;
  background: #fff;
  inset-block-start: 54px;
}

.select-item.v-menu > .v-overlay__content {
  position: sticky !important;
}
</style>

<!-- inset-block-start: -10px; -->
