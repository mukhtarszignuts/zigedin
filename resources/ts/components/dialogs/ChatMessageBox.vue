<script setup lang="ts">

import ChatLog from '@/views/apps/chat/ChatLog.vue'
import { useChat } from '@/views/apps/chat/useChat'
import { useChatStore } from '@/views/apps/chat/useChatStore'
import { useResponsiveLeftSidebar } from '@core/composable/useResponsiveSidebar'
import { avatarText } from '@core/utils/formatters'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useDisplay, useTheme } from 'vuetify'
import echo from "@/echo";

interface Props {
    isDialogVisible: boolean,
}

interface Emit {
    (e: 'update:isDialogVisible', val: boolean): void
}

const props = defineProps<Props>()

const emit = defineEmits <Emit>()

const dialogVisibleUpdate = (val: boolean) => {
    emit('update:isDialogVisible', val)
}

// composables
const vuetifyDisplays = useDisplay();
const store = useChatStore();
const { isLeftSidebarOpen } = useResponsiveLeftSidebar(
  vuetifyDisplays.smAndDown
);
const { resolveAvatarBadgeVariant } = useChat();

// Perfect scrollbar
const chatLogPS = ref<any>(null);

const scrollToBottomInChatLog = () => {
      if (chatLogPS.value !=null) {
        // Wait for the next DOM update
        nextTick(() => {
          // Use PerfectScrollbar's scroll API
          chatLogPS.value.$el.scrollTop = chatLogPS.value.$el.scrollHeight;
        });
      }
    };


// Open Sidebar in smAndDown when "start conversation" is clicked
const startConversation = () => {
  if (vuetifyDisplays.mdAndUp.value) return;
  isLeftSidebarOpen.value = true;
};

// Chat message
const msg = ref("");

const sendMessage = async () => {
  if (!msg.value) return;
  
  await store.sendMsg(msg.value);

  // Reset message input
  msg.value = "";

  scrollToBottomInChatLog();
  // Scroll to bottom
 
};


// file input
const refInputEl = ref<HTMLElement>()

onMounted(() => {
  scrollToBottomInChatLog();
});

   // Listen for new messages on the Echo channel
   echo.channel(`chat.${store.profileUser?.id}`)
        .listen('NewMessage', (e:any) => {
          const messageTime = new Date(e.message.created_at).toLocaleString('en-US', {
            timeZone: 'GMT',
            hour12: false,
          });

          const msg = {
            message: e.message.message,
            time: messageTime,
            senderId: e.message.receiver_id,
            feedback: {
              isSent: e.message.is_sent,
              isDelivered: e.message.is_delivered,
              isSeen: e.message.is_seen,
            }
          };

          if (store.activeChat?.chat?.messages) {
            store.activeChat.chat.messages.push(msg);
            // scrollToBottomInChatLog(); // Scroll to the bottom after each new message
            console.log('NewMessage event received:', msg);
          }
        });
</script>

<template>
  <VDialog :model-value="props.isDialogVisible" max-width="600" @update:model-value="dialogVisibleUpdate"
    scroll-strategy="none">
    <!-- 👉 Dialog close btn -->
    <DialogCloseBtn @click="$emit('update:isDialogVisible', false)" />

    <VCard class="share-project-dialog">
      <VCardText>

        <!-- 👉 Chat content -->
        <VMain class="chat-content-container">

          <!-- 👉 Right content: Active Chat -->
          <div v-if="store.activeChat" class="d-flex flex-column" style="max-height: 450px;">
            <!-- 👉 Active chat header -->
            <div class="active-chat-header d-flex align-center text-medium-emphasis bg-surface">
              <!-- Sidebar toggler -->
              <IconBtn class="d-md-none me-3" @click="isLeftSidebarOpen = true">
                <VIcon icon="tabler-menu-2" />
              </IconBtn>

              <!-- avatar -->
              <div class="d-flex align-center cursor-pointer" @click="true">
                <VBadge dot location="bottom right" offset-x="3" offset-y="0" :color="'primary'" bordered>
                  <VAvatar size="38" :variant="'tonal'" :color="undefined" class="cursor-pointer">
                    <VImg v-if="store.activeChat.contact.avatar" :src="store.activeChat.contact.avatar"
                      :alt="store.activeChat.contact.fullName" />
                    <span v-else>{{ avatarText(store.activeChat.contact.fullName) }}</span>
                  </VAvatar>
                </VBadge>

                <div class="flex-grow-1 ms-4 overflow-hidden">
                  <p class="text-h6 mb-0">
                    {{ store.activeChat.contact.fullName }}
                  </p>
                  <p class="text-truncate mb-0 text-disabled">
                    {{ store.activeChat.contact.about }}
                  </p>
                </div>
              </div>

              <VSpacer />

              <!-- Header right content -->
              <div class="d-sm-flex align-center d-none">
                <IconBtn>
                  <VIcon icon="tabler-phone-call" />
                </IconBtn>
                <IconBtn>
                  <VIcon icon="tabler-video" />
                </IconBtn>
                <IconBtn>
                  <VIcon icon="tabler-search" />
                </IconBtn>
              </div>

              <MoreBtn :menu-list="[]" density="comfortable" color="undefined" />
            </div>

            <VDivider />

            <!-- Chat log -->
            <PerfectScrollbar ref="chatLogPS" tag="ul" :options="{ wheelPropagation: false }" class="flex-grow-1">
              <ChatLog />
            </PerfectScrollbar>

            <!-- Message form -->
            <VForm class="chat-log-message-form mb-5 mx-5" @submit.prevent="sendMessage">
              <VTextField :key="store.activeChat?.contact.id" v-model="msg" variant="solo" class="chat-message-input"
                placeholder="Type your message..." density="default" autofocus>
                <template #append-inner>
                  <IconBtn>
                    <VIcon icon="tabler-microphone" />
                  </IconBtn>

                  <IconBtn class="me-2" @click="refInputEl?.click()">
                    <VIcon icon="tabler-photo" />
                  </IconBtn>

                  <VBtn @click="sendMessage">
                    Send
                  </VBtn>
                </template>
              </VTextField>

              <input ref="refInputEl" type="file" name="file" accept=".jpeg,.png,.jpg,GIF" hidden>
            </VForm>
          </div>

          <!-- 👉 Start conversation -->
          <div v-else class="d-flex h-100 align-center justify-center flex-column">
            <VAvatar size="109" class="elevation-3 mb-6 bg-surface">
              <VIcon size="50" class="rounded-0 text-high-emphasis" icon="tabler-message" />
            </VAvatar>
            <p class="mb-0 px-6 py-1 font-weight-medium text-lg elevation-3 rounded-xl text-high-emphasis bg-surface"
              :class="[{ 'cursor-pointer': $vuetify.display.smAndDown }]" @click="startConversation">
              Start Conversation
            </p>
          </div>
        </VMain>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<style lang="scss">
.share-project-dialog {
  .card-list {
    --v-card-list-gap: 1rem;
  }
}
</style>
