<script setup lang="tsx">
import type { ChatContact as TypeChatContact } from "@/@fake-db/types";
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useThemeConfig } from '@core/composable/useThemeConfig'
import { AppContentLayoutNav,NavbarType } from '@layouts/enums'
import { avatarText } from '@/@core/utils/formatters';
import { useChatStore } from "@/views/apps/chat/useChatStore";
import { useChat } from "@/views/apps/chat/useChat";
import { formatDate } from "@core/utils/formatters";
import ChatLeftSidebarContent from "@/views/apps/chat/ChatLeftSidebarContent.vue";
import $http from "@/plugins/axios";
import { useDisplay, useTheme } from "vuetify"
import { useResponsiveLeftSidebar } from "@core/composable/useResponsiveSidebar";
import  ChatMessageBox  from "@/components/dialogs/ChatMessageBox.vue"


// composables
const vuetifyDisplays = useDisplay();
const store = useChatStore();
const { isLeftSidebarOpen } = useResponsiveLeftSidebar(
  vuetifyDisplays.smAndDown
);
const { resolveAvatarBadgeVariant } = useChat();

// Perfect scrollbar
const chatLogPS = ref();

// const scrollToBottomInChatLog = () => {
//   const scrollEl = chatLogPS.value.$el || chatLogPS.value;

//   scrollEl.scrollTop = scrollEl.scrollHeight;
// };


const isNavDrawerOpen = ref(false)

const {
  appContentLayoutNav,
  isLessThanOverlayNavBreakpoint,
} = useThemeConfig()

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

const openDrawer = async()=>{
  // getUserList();
  isLeftSidebarOpen.value=true
  isNavDrawerOpen.value = true
}

//Need to change 
const openMessage = async (data:any) => {
  senderData.value=data
  console.log(senderData.value,'open message');
  isShareProjectDialogVisible.value = true;
    
}

/***
 * new code
 */

 // Chat message
const msg = ref("");

 const openChatOfContact = async (userId: TypeChatContact["id"]) => {

  
  await store.getChat(userId);

  // Reset message input
  msg.value = "";

  // Set unseenMsgs to 0
  const contact = store.chatsContacts.find((c) => c.id === userId);

  // if (contact) contact.chat.unseenMsgs = 0;
  // calling api for msg seen
  if (contact) contact.chat.unseenMsgs = 0;

  // if smAndDown =>  Close Chat & Contacts left sidebar
  if (vuetifyDisplays.smAndDown.value) isLeftSidebarOpen.value = false;

  // Scroll to bottom
  nextTick(() => {
    // scrollToBottomInChatLog();
  });

  isShareProjectDialogVisible.value=true;
};


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
        <!-- <CustomizerSection> -->
        <!-- 👉 RTL -->
        <!-- <div class="d-flex align-center justify-space-between">
            <AppTextField v-model="search" class="error-custom search-input w-100 ms-auto" placeholder="Search"
              @input="searchUser(search)" />
          </div> -->
        <!-- 
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
</VCardText> -->


        <!-- </CustomizerSection> -->
        <!-- !SECTION -->

        <CustomizerSection>
          <ChatLeftSidebarContent v-model:isDrawerOpen="isLeftSidebarOpen" v-model:search="q"
            @open-chat-of-contact="openChatOfContact" @show-user-profile="false" @close="isLeftSidebarOpen = false" />
        </CustomizerSection>
      </PerfectScrollbar>
    </VNavigationDrawer>
  </template>

  <ChatMessageBox v-if="isShareProjectDialogVisible" v-model:isDialogVisible="isShareProjectDialogVisible"
    @close-dialog="isShareProjectDialogVisible=false" />
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
