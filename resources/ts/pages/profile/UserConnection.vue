<script lang="ts" setup>

import { avatarText } from "@core/utils/formatters";

interface ProfileConnections{
  first_name: string
  last_name: string
  avatar: string
  image_url: string
  isFriend: boolean
  status: string
  request_date: string
  connection_count: number
}

interface Props {
  connectionsData: ProfileConnections[]
}
 interface Emit {
  (e: "viewAll", value: string): void;
  (e: "handleRequest", value: any): void;
 }

const props = defineProps<Props>()

const emit  = defineEmits<Emit>()

const handleClick =  (action:string,item:any) => {
      console.log('handleClick',item);
      
      const emitData={
          connection_id:item.id,
          status:action
        };
    emit("handleRequest",emitData);
}

const moreList = [
  { title: 'Share connections', value: 'Share connections' },
  { title: 'Suggest edits', value: 'Suggest edits' },
]
</script>

<template>
  <VCard title="Connection" class="mb-4">
    <template #append>
      <div class="me-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VList class="card-list">
        <VListItem v-for="data in props.connectionsData" :key="data?.first_name">
          <template #prepend>
            <VAvatar size="38" :variant="!data?.image_url ? 'tonal' : undefined" :color="'secondary'">
              <VImg v-if="data?.image_url" :src="data?.image_url" />
              <span v-else>{{ avatarText(data?.first_name) }}</span>
            </VAvatar>

          </template>

          <VListItemTitle class="font-weight-medium">
            {{ data.first_name }} {{ data.last_name }}
          </VListItemTitle>
          <VListItemSubtitle>{{ data?.connection_count }} Mutual Connections</VListItemSubtitle>

          <template #append>
            <VBtn icon size="30" rounded class="me-2" :variant="'outlined'" @click="handleClick('A',data)">
              <VIcon size="20" :icon="'tabler-check'" />
            </VBtn>
            <VBtn icon size="30" rounded :variant="'outlined'" :color="'secondary'" @click="handleClick('R',data)">
              <VIcon size="20" :icon="'tabler-x'" />
            </VBtn>
          </template>
        </VListItem>

        <VListItem>
          <VListItemTitle>
            <VBtn block variant="text" @click="emit('viewAll','c')">
              View all connections
            </VBtn>
          </VListItemTitle>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 14px;
}
</style>
