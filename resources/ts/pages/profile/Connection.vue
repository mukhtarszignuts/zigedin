<script setup lang="ts">

import { useRoute } from 'vue-router'
import { fetchConnectionslist , connected , disconnected , connectionRequest , suggestedConnection } from '@/services/ConnectionService'
import { avatarText } from '@/@core/utils/formatters'

interface ProfileConnections{
  // isFriend: boolean
  status: string
  request_date: string
  user_id:number
  connection_id:number
  friend:Friend
  user:Friend
}

interface Friend{
  first_name: string
  last_name: string
  avatar: string
  image_url: string
  headline?: string
  connection_count: number
}

interface SuggestedFriend{
  id:number
  first_name: string
  last_name: string
  profile_image: string
  image_url: string
  headline?: string
  connection_count: number
}

// interface Props {
//   connectionsData: ProfileConnections[]
// }
//  interface Emit {
//   (e: "viewAll", value: string): void;
//  }

// const props = defineProps<Props>()

// const emit  = defineEmits<Emit>()
const router = useRoute()
const receiverData = ref<ProfileConnections[]>([])
const senderData = ref<ProfileConnections[]>([])
const suggestionData = ref<SuggestedFriend[]>([]);

const fetchConnectionData = async () => {
    const data = await fetchConnectionslist();
    receiverData.value = data.receiver
    senderData.value = data.sender
}

watch(router, fetchConnectionData, { immediate: true })

const actionList = (item: any) => {
  
  const actions = [
    {
      title: "Share",
      id: "1",
      prependIcon: "tabler-eye",
      action: "share",
      show: true,
    },
    {
      title: "Block",
      id: "1",
      prependIcon: "tabler-eye",
      action: "block",
      show: true,
    },
    {
      title: "Remove Connection",
      id: "1",
      prependIcon: "tabler-circle-x",
      action: "delete",
      show: true,
    },
  ];
  return actions
};

const handleMoreAction = async (action: any, item: any) => {
 switch (action) {
  case '':
    
    break;
  case 'connect':
    console.log('connected item' , item);
    const response = await connected(item.id);
    await fetchConnectionData()
    await suggestConnectionList()
    console.log('connected response' , response);
    
    break;
  case 'delete':
    const data  = await disconnected(item.connection_id);
    console.log('action delete:',data);
    await fetchConnectionData()
    await suggestConnectionList()
    break;
 
  default:
    break;
 }
};

// handle request
const handleRequest = async (connection_id:number,status:string) => {
  const params={
              connection_id:connection_id,
              status:status
            }
  const response = await connectionRequest(params);
  await fetchConnectionData()
  await suggestConnectionList()
  console.log('handle request:',connection_id, 'status:',status);
}

// fetch user list 
const suggestConnectionList = async () =>{
  const data = await suggestedConnection();
  suggestionData.value = data
}

onMounted(async () => {
   await fetchConnectionData()
   await suggestConnectionList()
})
</script>

<template>
  <div>
    <!-- <VDivider class="mb-2" /> -->
    <h2 class="mb-2 mt-2" v-if="receiverData?.length">Invitaions</h2>
    <VRow>
      <VCol v-for="data in receiverData" :key="data.request_date" sm="6" lg="3" cols="12">
        <VCard>
          <div class="vertical-more">
            <IconBtn>
              <VIcon icon="mdi-dots-vertical" />
              <VMenu activator="parent" open-on-click>
                <VCard>
                  <VList v-for="i in actionList(data).filter(action => action.show !== false)" :key="i?.id"
                    class="pa-1">
                    <VListItem @click="handleMoreAction(i?.action, data)">
                      <template #prepend>
                        <VIcon :icon="i.prependIcon" />
                      </template>
                      <template #title>
                        {{ i?.title }}
                      </template>
                    </VListItem>
                  </VList>
                </VCard>
              </VMenu>
            </IconBtn>
          </div>
          <VCardItem>
            <VCardTitle class="d-flex flex-column align-center justify-center">
              <VAvatar size="100" :variant="!data?.user?.image_url ? 'tonal' : 'elevated'" :color="'warning'">
                <VImg v-if="data?.user?.image_url" :src="data?.user?.image_url" />
                <span v-else>{{ avatarText(data?.user?.first_name) }}</span>
              </VAvatar>
              <p class="mt-4 mb-0">
                {{ data?.user?.first_name }} {{ data?.user?.last_name }}
              </p>
              <span class="text-body-1">{{ data?.user?.headline }}</span>

              <div class="d-flex align-center flex-wrap">
                <h6> Other {{ data?.user?.connection_count }} Mutual Connections</h6>
              </div>
            </VCardTitle>
          </VCardItem>

          <VCardText>
            <div class="d-flex justify-center gap-4">
              <!-- <VBtn :prepend-icon="data.status==='A' ? 'tabler-user-check' : 'tabler-user-plus'"
                :variant="data.status==='A' ? 'elevated' : 'tonal'" >
                {{ data.status==='A' ? 'connected' : 'connect' }}
              </VBtn>
              <IconBtn variant="tonal" class="rounded">
                <VIcon icon="tabler-mail" />
              </IconBtn> -->

              <IconBtn variant="outlined" rounded :color="'primary'" @click="handleRequest(data?.user_id,'A')">
                <VIcon icon="tabler-check" />
              </IconBtn>
              <IconBtn variant="outlined" rounded :color="'secondary'" @click="handleRequest(data?.user_id,'R')">
                <VIcon icon="tabler-x" />
              </IconBtn>
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <h2 class="mb-2 mt-2" v-if="senderData?.length">Connections</h2>

    <VRow>
      <VCol v-for="data in senderData" :key="data.request_date" sm="6" lg="3" cols="12">
        <VCard>
          <div class="vertical-more">
            <IconBtn>
              <VIcon icon="mdi-dots-vertical" />
              <VMenu activator="parent" open-on-click>
                <VCard>
                  <VList v-for="i in actionList(data).filter(action => action.show !== false)" :key="i?.id"
                    class="pa-1">
                    <VListItem @click="handleMoreAction(i?.action, data)">
                      <template #prepend>
                        <VIcon :icon="i.prependIcon" />
                      </template>
                      <template #title>
                        {{ i?.title }}
                      </template>
                    </VListItem>
                  </VList>
                </VCard>
              </VMenu>
            </IconBtn>
          </div>
          <VCardItem>
            <VCardTitle class="d-flex flex-column align-center justify-center">
              <VAvatar size="100" :variant="!data?.friend?.image_url ? 'tonal' : 'elevated'" :color="'secondary'">
                <VImg v-if="data?.friend?.image_url" :src="data?.friend?.image_url" />
                <span v-else>{{ avatarText(data?.friend?.first_name) }}</span>
              </VAvatar>
              <p class="mt-4 mb-0">
                {{ data?.friend?.first_name }} {{ data?.friend?.last_name }}
              </p>
              <span class="text-body-1">{{ data?.friend?.headline }}</span>

              <div class="d-flex align-center flex-wrap">
                <h6>{{data?.friend?.connection_count}} Mutual Connections</h6>
              </div>
            </VCardTitle>
          </VCardItem>

          <VCardText>
            <div class="d-flex justify-center gap-4">
              <VBtn v-if="data.status==='P'" rounded
                :prepend-icon="data.status==='P' ? 'tabler-user-minus' : 'tabler-user-plus'" :variant="'outlined'"
                @click="handleMoreAction('delete',data)">
                {{ data.status==='P' ? 'withdraw' : 'connected' }}
              </VBtn>

              <VBtn v-else rounded :prepend-icon="data.status==='P' ? 'tabler-user-minus' : 'tabler-user-plus'"
                :variant="'elevated'">
                {{ data.status==='P' ? 'withdraw' : 'connected' }}
              </VBtn>
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <h2 class="mb-2 mt-2" v-if="suggestionData?.length">Suggestion Connections</h2>
    <VRow>
      <VCol v-for="data in suggestionData" :key="data.id" sm="6" lg="3" cols="12">
        <VCard>
          <div class="vertical-more">
            <!-- <MoreBtn :menu-list="[
                { title: 'Share connection', value: 'Share connection' },
                { title: 'Block connection', value: 'Block connection' },
                { type: 'divider', class: 'my-2' },
                { title: 'Delete', value: 'Delete', class: 'text-error' },
              ]" item-props /> -->
          </div>
          <VCardItem>
            <VCardTitle class="d-flex flex-column align-center justify-center">

              <VAvatar size="100" :variant="!data?.image_url ? 'tonal' : 'elevated'" :color="'primary'">
                <VImg v-if="data?.image_url" :src="data?.image_url" />
                <span v-else>{{ avatarText(data?.first_name) }}</span>
              </VAvatar>

              <p class="mt-4 mb-0">
                {{ data?.first_name }} {{ data?.last_name }}
              </p>
              <span class="text-body-1">{{ data?.headline }}</span>

              <div class="d-flex align-center flex-wrap">
                <h6>{{data?.connection_count}} Mutual Connections</h6>
              </div>
            </VCardTitle>
          </VCardItem>

          <VCardText>
            <div class="d-flex justify-center gap-4">
              <!-- <VBtn :prepend-icon="data.status==='A' ? 'tabler-user-check' : 'tabler-user-plus'"
                :variant="data.status==='A' ? 'elevated' : 'tonal'">
                {{ data.status==='A' ? 'connected' : 'connect' }}
              </VBtn> -->

              <VBtn :prepend-icon="'tabler-user-plus'" :variant="'outlined'" rounded
                @click="handleMoreAction('connect',data)">
                {{ 'connect' }}
              </VBtn>
              <!-- <IconBtn variant="tonal" class="rounded">
                <VIcon icon="tabler-mail" />
              </IconBtn> -->
            </div>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>

<style lang="scss">
.vertical-more {
  position: absolute;
  inset-block-start: 1rem;
  inset-inline-end: 0.5rem;
}
</style>
