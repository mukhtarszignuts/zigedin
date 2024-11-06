<script setup lang="ts">
import type { DealType } from './types'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
// import sittingGirlWithLaptop from '@images/illustrations/sitting-girl-with-laptop.png'
import sittingGirlWithLaptop from '@images/page.png'
import mobilePage from '@images/mobile-page.png'
import CreateDealBackgroundDark from '@images/pages/DealTypeBackground-dark.png'
import CreateDealBackgroundLight from '@images/pages/DealTypeBackground-light.png'

const props = defineProps<{
  formData: DealType
}>()

const emit = defineEmits<{
  (e: 'update:formData', value: DealType): void
}>()

const createDealBackground = useGenerateImageVariant(CreateDealBackgroundLight, CreateDealBackgroundDark)

const discountOffers = [
  {
    icon: { icon: 'custom-check', size: '40' },
    title: 'Company',
    desc: 'Small , medium , and large businesses',
    value: 'company',
    color: 'primary',
  },
  {
    icon: { icon: 'custom-card', size: '40' },
    title: 'Showcase Page',
    desc: 'Sub-pages associated with an exiting page',
    value: 'showcash',
  },
  {
    icon: { icon: 'custom-diamond', size: '40' },
    title: 'Educational Institution',
    desc: 'School and Universities',
    value: 'school',
  },
]

const formData = ref<DealType>(props.formData)

watch(formData, () => {
  emit('update:formData', formData.value)
})
</script>

<template>
  <VForm>
    <VRow>
      <!-- 👉 Shopping girl image -->

      <VCol cols="12">
        <h1 class="text-center">Create a ZigedIn Page</h1>
        <p class="text-center">Connect with clients, employees, and the ZigedIn community. To get started, choose a
          page type.</p>
      </VCol>

      <VCol cols="12">
        <CustomRadiosWithIcon v-model:selected-radio="formData.Offer" :radio-content="discountOffers"
          :grid-column="{ cols: '12', sm: '4' }" />
      </VCol>

      <VCol cols="12">
        <div class="d-flex align-center justify-center w-100 deal-type-image-wrapper border rounded px-5 ">
          <VImg :src="sittingGirlWithLaptop" class="mt-3" />
          <VImg :src="mobilePage" class="mt-3 " />
          <VImg :src="createDealBackground" class="position-absolute deal-type-background-img" />
        </div>
      </VCol>

      <!-- <VCol cols="12" sm="6">
        <AppTextField v-model="formData.discount" type="number" label="Discount"
          hint="Enter the discount percentage. 10 = 10%" persistent-hint />
      </VCol> -->

      <!-- <VCol cols="12" sm="6">
        <AppSelect v-model="formData.region" label="Region"
          :items="['Asia', 'Europe', 'Africa', 'Australia', 'North America', 'South America']"
          hint="Select applicable regions for the deal." persistent-hint />
      </VCol> -->
    </VRow>
  </VForm>
</template>

<style lang="scss">
.deal-type-image-wrapper {
  position: relative;
  block-size: 260px;
  inline-size: 210px;
}

.deal-type-background-img {
  inline-size: 75%;
  inset-block-end: 0;
}
</style>
