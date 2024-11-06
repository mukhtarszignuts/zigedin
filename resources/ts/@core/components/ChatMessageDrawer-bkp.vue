<script setup lang="tsx">
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useTheme } from 'vuetify'
import { staticPrimaryColor } from '@/plugins/vuetify/theme'
import { useThemeConfig } from '@core/composable/useThemeConfig'
import { RouteTransitions, Skins } from '@core/enums'
import { AppContentLayoutNav, ContentWidth, FooterType, NavbarType } from '@layouts/enums'
import { themeConfig } from '@themeConfig'
import { avatarText } from '@/@core/utils/formatters';

import $http from "@/plugins/axios";
// import { useTheme } from 'vuetify'

const isNavDrawerOpen = ref(false)

const {
  theme,
  skin,
  appRouteTransition,
  navbarType,
  footerType,
  isVerticalNavCollapsed,
  isVerticalNavSemiDark,
  appContentWidth,
  appContentLayoutNav,
  isAppRtl,
  isNavbarBlurEnabled,
  isLessThanOverlayNavBreakpoint,
} = useThemeConfig()

// 👉 Primary Color
const vuetifyTheme = useTheme()

// const vuetifyThemesName = Object.keys(vuetifyTheme.themes.value)

const initialThemeColors = JSON.parse(JSON.stringify(vuetifyTheme.current.value.colors))
const colors = ['primary', 'secondary', 'success', 'info', 'warning', 'error']

// ℹ️ It will set primary color for current theme only
const setPrimaryColor = (color: string) => {
  const currentThemeName = vuetifyTheme.name.value

  vuetifyTheme.themes.value[currentThemeName].colors.primary = color

  // ℹ️ We need to store this color value in localStorage so vuetify plugin can pick on next reload
  localStorage.setItem(`${themeConfig.app.title}-${currentThemeName}ThemePrimaryColor`, color)

  // ℹ️ Update initial loader color
  localStorage.setItem(`${themeConfig.app.title}-initial-loader-color`, color)
}

/*
  ℹ️ This will return static color for first indexed color
  If we don't make first (primary) color as static then when another color is selected then we will have two theme colors with same hex codes and it will show two check marks
*/
const getBoxColor = (color: string, index: number) => index ? color : staticPrimaryColor

const { width: windowWidth } = useWindowSize()

const headerValues = computed(() => {
  const entries = Object.entries(NavbarType)

  if (appContentLayoutNav.value === AppContentLayoutNav.Horizontal)
    return entries.filter(([_, val]) => val !== NavbarType.Hidden)

  return entries
})

interface Member {
  avatar: string
  name: string
  first_name: string
  last_name: string
  email: string

}

const usersList = ref<Member[]>([]);

const count = ref<number>(0);

const perPage = ref<number>(10);
const currentPage = ref(1);
const sortKey = ref<string>("created_at");
const sortOrder = ref<string>("desc");
const search = ref<string>("");
const isShareProjectDialogVisible = ref<boolean>(false);
const senderData = ref<any>();

// search users
const searchUser = async (data: any) => {
  if (data.length > 2 || data.length === 0) {
    currentPage.value = 1;
    await getUserList();
  }
};

//! Need to change this 

const getUserList = async () => {
  try {
    const input = {
      sort_field: sortKey.value,
      sort_order: sortOrder.value,
      page: currentPage.value,
      per_page: perPage.value,
      search: search.value,
    };

    const { data:data } =  await $http.post("admin/user/list", input);

    if (data) {
      usersList.value = data.data.users;
      count.value = data.count;  
    }
  } catch (e) {
    console.log(e);
  }
};

const openDrawer = async()=>{
  getUserList();
  isNavDrawerOpen.value = true
}

const openMessage = async (data:any) => {
  senderData.value=data
  console.log(senderData.value,'open message');
  isShareProjectDialogVisible.value = true;
    
}

// Search query
const q = ref("");

//! Need to change
watch(q, (val) => store.fetchChatsAndContacts(val), { immediate: true });

</script>

<template>
  <template v-if="!isLessThanOverlayNavBreakpoint(windowWidth)">
    <VBtn icon size="small" class="app-customizer-toggler rounded-s-lg rounded-0" style="z-index: 1001;"
      @click="openDrawer">
      <VIcon size="22" icon="tabler-settings" />
    </VBtn>

    <VNavigationDrawer v-model="isNavDrawerOpen" temporary border="0" location="end" width="400" :scrim="false"
      class="app-customizer">
      <!-- 👉 Header -->
      <div class="customizer-heading d-flex align-center justify-space-between">
        <div>
          <h6 class="text-h6">
            Messaging
          </h6>

        </div>
        <IconBtn @click="isNavDrawerOpen = false">
          <VIcon icon="tabler-x" size="20" />
        </IconBtn>
      </div>

      <VDivider />

      <PerfectScrollbar tag="ul" :options="{ wheelPropagation: false }">

        <!-- SECTION MISC -->
        <CustomizerSection>
          <!-- 👉 RTL -->
          <div class="d-flex align-center justify-space-between">
            <AppTextField v-model="search" class="error-custom search-input w-100 ms-auto" placeholder="Search"
              @input="searchUser(search)" />
          </div>

          <!-- 👉 Route Transition -->
          <VCardText>
            <VList class="card-list">
              <VListItem v-for="data in usersList" :key="data?.first_name" @click="openMessage(data)">
                <template #prepend>
                  <VAvatar size="38" :variant="!data?.avtar ? 'tonal' : undefined" :color="'secondary'">
                    <VImg v-if="data?.avtar" :src="data?.avtar" />
                    <span class="" v-else>{{ avatarText(data?.first_name) }}</span>
                  </VAvatar>
                </template>
                <VListItemTitle class="font-weight-medium">
                  {{ data.first_name }} {{ data.last_name }}
                </VListItemTitle>
                <VListItemSubtitle>{{ data?.email }} </VListItemSubtitle>
              </VListItem>
            </VList>
          </VCardText>


        </CustomizerSection>

        <CustomizerSection>
          <ChatLeftSidebarContent v-model:isDrawerOpen="isNavDrawerOpen" v-model:search="q"
            @open-chat-of-contact="openChatOfContact" @show-user-profile="true" @close="isNavDrawerOpen = false" />
        </CustomizerSection>
        <!-- !SECTION -->
      </PerfectScrollbar>
    </VNavigationDrawer>
  </template>
  <ChatMessageBox v-if="isShareProjectDialogVisible" v-model:isDialogVisible="isShareProjectDialogVisible"
    :sender-data="senderData" @close-dialog="isShareProjectDialogVisible=false" />
</template>

<style lang="scss">
.app-customizer {
  .customizer-section {
    padding: 1.25rem;
  }

  .customizer-heading {
    padding-block: 0.875rem;
    padding-inline: 1.25rem;
  }

  .v-navigation-drawer__content {
    display: flex;
    flex-direction: column;
  }
}

.app-customizer-toggler {
  position: fixed !important;
  inset-block-start: 50%;
  inset-inline-end: 0;
}
</style>
