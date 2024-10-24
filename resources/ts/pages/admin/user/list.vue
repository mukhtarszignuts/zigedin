<script setup lang="ts">
import { isEmptyArray } from "@/@core/utils";
import AddEditUserDialog from "@/components/dialogs/AddEditUserDialog.vue";
import useFormatting from "@/composable/useFormatting";
import axios from "@/plugins/axios";
import { AddEditUser, deleteUser, fetchUserList, getUserdata } from "@/services/UserService";
import { avatarText } from "@core/utils/formatters";
import noData from "@images/no_data.svg";
import { useHead } from "@vueuse/head";
import CryptoJs from "crypto-js";
import moment from "moment";
import { useRouter } from "vue-router";
import { toast } from "vue3-toastify";

interface UsersData {
  id: number;
  first_name: string;
  last_name: string;
  phone: string;
  email: string;
  role: string;
  is_active: boolean;
  profile_image: string;
  image_url: string;
  created_at: string;
}



const router = useRouter();
const search = ref<string>();
const roleFilter = ref<string>();
const statusFilter = ref<string>();
const isEdit = ref<boolean>(false);
const userList = ref<UsersData[]>([]);
const count = ref<number>(0);
const perPage = ref<number>(10);
const lengthPage = ref<number>(0);
const currentPage = ref(1);
const sortKey = ref<string>("created_at");
const sortOrder = ref<string>("desc");
const selected = ref([]);
const allSelectedType = ref<boolean>(false);
const deletedId = ref<number>();
const bulkDeleteIds = ref([]);
const isConfirmDialog = ref<boolean>(false);
const isBulkDeleteBox = ref<boolean>(false);
const isAddNewUserDrawerVisible = ref<boolean>(false);
const isDeleteDisabled = ref<boolean>(true);
const isLoading = ref<boolean>(false);
const addEditUserData = ref<object | null>({});
const userData = ref<any>({});
const filerDate = ref<any>();

const { roles, roleVariant , statusVariant } = useFormatting();

const pageCountList = ref<any[]>([
  {
    label: "10",
    value: 10,
  },

  {
    label: "25",
    value: 25,
  },
  {
    label: "50",
    value: 50,
  },
  {
    label: "100",
    value: 100,
  },
]);

// headers
const headers = [
  { title: "NO", key: "no" },
  { title: "NAME", key: "name" },
  { title: "EMAIL", key: "email" },
  { title: "PHONE", key: "phone" },
  { title: "ROLE", key: "role" },
  { title: "STATUS", key: "status" },
  { title: "CREATED AT", key: "created_at" },
  { title: "ACTION", key: "action" },
];

// search user
const searchUser = async (data: any) => {
  if (data.length > 2 || data.length === 0) {
    currentPage.value = 1;
    await getUserList();
  }
};

const getUserList = async () => {
  try {
    const input = {
      sort_field: sortKey.value,
      sort_order: sortOrder.value,
      page: currentPage.value,
      per_page: perPage.value,
      search: search.value,
      role: roleFilter.value,
      start_date: filerDate.value
        ? filerDate.value.includes("to")
          ? filerDate.value.split(" to ")[0]
          : filerDate?.value
        : "",
      end_date: filerDate.value
        ? filerDate.value.includes("to")
          ? filerDate.value.split(" to ")[1]
          : filerDate?.value
        : "",
    };

    const data = await fetchUserList(input);

    if (data) {
      userList.value = data.users;
      count.value = data.count;
      lengthPage.value = Math.ceil(count.value / perPage.value);
    }
  } catch (e) {
    console.log(e);
  }
};

// delete User
const userDelete = async () => {
  try {
    isLoading.value = true;
    const data = await deleteUser(deletedId.value);
    if (data) {
      toast.success(`User Delete Successfully...!`);
      await getUserList();
      isConfirmDialog.value = false;
      isLoading.value = false;
    }
  } catch (e) {
    console.error(e);
    isConfirmDialog.value = false;
    isLoading.value = false;
  }
};

// TODO Need to chage
const actionList = (item: any) => {
  const actions = [
    // {
    //   title: "Details",
    //   id: "1",
    //   prependIcon: "tabler-eye",
    //   action: "viewDetails",
    //   show: true,
    // },
    {
      title: "Edit",
      id: "1",
      prependIcon: "tabler-edit",
      action: "Edit",
      show: true,
    },
    {
      title: "Delete",
      id: "1",
      prependIcon: "tabler-trash",
      action: "Delete",
      show: true,
    },
  ];
  return actions;
};

const handleMoreAction = (action: any, item: any) => {
  if (action === "viewDetails") {
    router.push({
      path: `/admin/user/${item.id}`,
    });
  } else if (action === "Edit") {
    EditUser(item.id);
  } else if (action === "Delete") {
    deletedId.value = item.id;
    isConfirmDialog.value = true;
    // deleteUser();
  }
};

const fromNumber = computed(() => {
  return currentPage.value * perPage.value - perPage.value + 1;
});

const toNumber = computed(() => {
  return count.value > currentPage.value * perPage.value
    ? currentPage.value * perPage.value
    : count.value;
});

const ResetFilter = () => {
  search.value = undefined;
  roleFilter.value = undefined;
  // companyFilter.value = undefined;
  statusFilter.value = undefined;
  filerDate.value = undefined;
  getUserList();
};

const allSelect = (i: boolean) => {
  if (i) {
    if (
      userList.value &&
      Array.isArray(userList.value) &&
      userList.value.length
    ) {
      selected.value = [];
      userList.value.forEach((obj: any) => {
        const matchingObject = obj.id;
        if (matchingObject) selected.value.push(matchingObject);
      });
    }
  } else {
    selected.value = [];
  }
};

// status change
const stausChange = async (id:number) => {
    try {
       const { data } = await axios.get(`admin/user/status-change/${id}`)
       if(data){
         toast.success(data.message)
         await getUserList();
       }
     } catch (e) {
        toast.error(e.response.data.message)
       console.log(e);
     }
}

// 👉 Add new user
const AddNewUser = async (userData: any) => {
  try {
    isLoading.value = true;
    const input = {
      first_name: userData.first_name,
      last_name: userData.last_name,
      phone: userData.phone,
      is_active: userData.is_active,
      role: userData.role,
      email: userData.email,
      city: userData.city,
      headline: userData.headline,
      profile_image: userData.profile_image,
      id: userData.isUpdate === true ? userData.id : null,
    };

    const formData = new FormData();

    // Append non-file data to FormData
    Object.keys(input).forEach((key) => {
      if (input[key] !== null && input[key] !== undefined) {
        formData.append(key, input[key]);
      }
    });

    const data = await AddEditUser(formData, userData.isUpdate);

    if (data) {
      toast.success(data.message);
      isAddNewUserDrawerVisible.value = false;
      getUserList();
      isLoading.value = false;
    } else {
      isAddNewUserDrawerVisible.value = false;
      isLoading.value = false;
    }
  } catch (e) {
    toast.error(e.data.data.message);
    isLoading.value = false;
  }
};

// Edit User
const EditUser = async (id: number) => {
  try {
    const data = await getUserdata(id);
    console.log(data.user);
    
    if (data) {
      addEditUserData.value = data.user;
      (isEdit.value = true), (isAddNewUserDrawerVisible.value = true);
    }
  } catch (e) {
    console.log(e);
  }
};

// bluk delete
const blukDelete = async () => {
  try {
    const input = {
      ids: bulkDeleteIds.value,
    };

    const { data } = await axios.post("user/bulk-delete", input);

    if (data) {
      isDeleteDisabled.value = false;
      isBulkDeleteBox.value = false;
      selected.value = [];
      bulkDeleteIds.value = [];
      await getUserList();
      toast.success("Users Delete Successfully..!");
    }
  } catch (e) {
    console.log(e);
  }
};

onMounted(() => {
  getUserList();
  const encryptedData = localStorage.getItem("user-data");
  const user = encryptedData
    ? JSON.parse(
        CryptoJs.AES.decrypt(
          encryptedData || "",
          import.meta.env.VITE_CRYPTO_SECURE_KEY
        ).toString(CryptoJs.enc.Utf8)
      )
    : null;

  userData.value = user;
  // if (user.role === "M") {
  //   companyId.value = user.company_id;
  // }
});

watch(
  async () => currentPage.value,
  async (val) => {
    if (await val) {
      await getUserList();
    }
  }
);

watch(
  async () => filerDate.value,
  async (val) => {
    if (await val) {
      await getUserList();
    }
  }
);

watch(
  async () => perPage.value,
  async (val) => {
    if (await val) {
      currentPage.value = 1;
      await getUserList();
    }
  }
);

watch(
  async () => roleFilter.value,
  async (val) => {
    currentPage.value = 1;
    await getUserList();
  }
);

watch(
  async () => statusFilter.value,
  async (val) => {
    currentPage.value = 1;
    await getUserList();
  }
);

watch(
  async () => selected.value,
  async (val) => {
    if (await val) {
      isDeleteDisabled.value = true;
      bulkDeleteIds.value = [];
      selected.value.forEach((id: any) => {
        const matchingDeleteObject = userList.value.find(
          (obj: any) => obj.id === id
        );

        if (matchingDeleteObject) {
          isDeleteDisabled.value = false;
          bulkDeleteIds.value.push(matchingDeleteObject.id);
        }
      });
    }
  }
);

useHead({
  title: "ZigedIn| User List",
});
</script>
<template>
  <div>
    <VCard title="User List">
      <VCardText>
        <VCardText>
          <VRow>
            <VCol cols="12" class="justify-end">
              <AppTextField v-model="search" class="error-custom search-input w-25 ms-auto" placeholder="Search"
                @input="searchUser(search)" />
            </VCol>
          </VRow>
          <VRow class="px-3">
            <VCol lg="2" md="3" sm="12" cols="12">
              <div class="d-flex align-center filter-box-width">
                Show
                <div>
                  <AppSelect v-model="perPage" :items="pageCountList" item-value="value" item-title="label"
                    class="mx-2" />
                </div>
                Entries
              </div>
            </VCol>
            <VCol md="9" lg="10" sm="12" cols="12">
              <div class="d-flex gap-3 common-filter justify-end">
                <AppDateTimePicker v-model="filerDate" class="w-100" placeholder="Select date"
                  :config="{ mode: 'range' }" />
                <!--  by Username, Email, Contact number -->

                <AppSelect v-if="userData?.role === 'A'" v-model="roleFilter" :items="roles" item-title="name"
                  item-value="id" placeholder="Filter by Role" class="w-5" clearable />


                <div class="d-flex common-filter gap-3">
                  <VBtn :disabled="!roleFilter && !statusFilter && !filerDate" @click="ResetFilter">
                    Reset Filter
                  </VBtn>
                  <div class="gap-3 d-flex flex-column">
                    <div class="d-flex gap-3 flex-wrap">
                      <!-- <VBtn
                        class="mr-0"
                        color="primary"
                        :disabled="isDeleteDisabled"
                        @click="isBulkDeleteBox = true"
                      >
                        Bulk Delete
                      </VBtn> -->

                      <VBtn class="mr-0" color="primary" @click="
                          (addEditUserData = null),
                            (isEdit = false),
                            (isAddNewUserDrawerVisible = true)
                        ">
                        Add User
                      </VBtn>
                    </div>
                  </div>
                </div>
              </div>
            </VCol>
          </VRow>
        </VCardText>
        <div v-if="!isEmptyArray(userList)" class="table-container">
          <VTable :items-per-page="perPage">
            <thead>
              <tr>
                <th>
                  <VCheckbox v-model="allSelectedType" @click="allSelect(!allSelectedType)" />
                </th>
                <th v-for="(item, index) in headers">
                  {{ item.key }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in userList" :key="item.id">
                <td>
                  <VCheckbox v-model="selected" :value="item?.id" />
                </td>
                <td>
                  {{
                  (currentPage === 1
                  ? count
                  : count - (currentPage - 1) * perPage) - index
                  }}
                </td>
                <td>

                  <div class="d-flex align-center">
                    <VAvatar size="38" :variant="!item?.profile_image ? 'tonal' : undefined" :color="'secondary'"
                      class="me-3">
                      <VImg v-if="item?.image_url" :src="item?.image_url" />
                      <span v-else>{{ avatarText(item?.first_name) }}</span>
                    </VAvatar>
                    <div class="d-flex flex-column">
                      <h6 class="text-body-1 font-weight-medium">
                        <span class="user-list-name text-primary">
                          {{ item?.first_name }}

                          {{ item?.last_name }}

                        </span>
                      </h6>
                    </div>
                  </div>
                </td>
                <td>
                  {{ item.email ?? " - " }}
                </td>
                <td>
                  {{ item.phone ?? " - " }}
                </td>
                <td>
                  <VChip label :color="roleVariant(item.role).color" class="font-weight-medium" size="small">
                    {{ roleVariant(item.role).text }}
                  </VChip>
                </td>
                <td>
                  <VSwitch :model-value=" item.is_active" @click="stausChange(item.id)" />
                  <!-- <VChip
                    :color="statusVariant(item.status).color"
                    class="font-weight-medium"
                    size="small"
                  >
                    {{ statusVariant(item.status).text }}
                  </VChip> -->
                </td>
                <td>
                  {{
                  item.created_at
                  ? moment(item.created_at).format("YYYY-MM-DD HH:MM:ss")
                  : " - "
                  }}
                </td>
                <td>
                  <div class="d-flex">
                    <IconBtn>
                      <VIcon icon="mdi-dots-vertical" />
                      <VMenu activator="parent" open-on-click>
                        <VCard>
                          <VList v-for="i in actionList(item).filter(
                              (action) => action.show !== false
                            )" :key="i?.id" class="pa-1">
                            <VListItem @click="handleMoreAction(i?.action, item)">
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
                </td>
              </tr>
            </tbody>
          </VTable>
        </div>
        <div v-else>
          <VImg :src="noData" alt="no data" height="450px" />
        </div>

        <div v-if="!isEmptyArray(userList)" class="d-flex mx-7 mt-5">
          <VRow v-if="!isEmptyArray(userList)">
            <VCol class="d-flex align-center">
              <div>
                Showing {{ fromNumber }} to {{ toNumber }} of
                {{ count }} entries
              </div>
            </VCol>
            <VCol>
              <div class="right-aligned-pagination">
                <VPagination v-model="currentPage" :total-visible="10" :length="lengthPage" />
              </div>
            </VCol>
          </VRow>
        </div>
      </VCardText>
    </VCard>

    <!-- Single delete user -->
    <VDialog v-model="isConfirmDialog" persistent class="v-dialog-sm">
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isConfirmDialog = !isConfirmDialog" />
      <!-- Dialog Content -->
      <VCard>
        <VCardText>
          <div class="text-center mt-5">
            <h2>Are You Sure You Want to delete ?</h2>
          </div>
        </VCardText>
        <VCardText class="d-flex justify-center gap-3 flex-wrap">
          <VBtn color="error" variant="tonal" @click="isConfirmDialog = false">
            No
          </VBtn>
          <VBtn @click="userDelete" color="success"> Yes </VBtn>
        </VCardText>
      </VCard>
    </VDialog>

    <!-- Bluk Delete User  -->
    <VDialog v-model="isBulkDeleteBox" persistent class="v-dialog-sm">
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isBulkDeleteBox = !isBulkDeleteBox" />
      <!-- Dialog Content -->
      <VCard>
        <v-card-title primary-title class="text-center">
          Please Confirm..!
        </v-card-title>
        <VCardText>
          <div class="text-center mt-5">
            <h2>Are You Sure You Want to delete ?</h2>
          </div>
        </VCardText>
        <VCardText class="d-flex justify-center gap-3 flex-wrap">
          <VBtn color="error" variant="tonal" @click="isBulkDeleteBox = false">
            No
          </VBtn>
          <VBtn @click="blukDelete()" color="success"> Yes </VBtn>
        </VCardText>
      </VCard>
    </VDialog>

    <!-- User info Update  -->
    <AddEditUserDialog v-if="isAddNewUserDrawerVisible" :is-drawer-open="isAddNewUserDrawerVisible" :is-edit="isEdit"
      :user-data="addEditUserData" :roles="roles" @close-dialog="isAddNewUserDrawerVisible = false"
      @user-data="AddNewUser" />


  </div>
</template>
<style lang="scss">
.common-filter .app-select.flex-grow-1 {
  min-width: 160px !important;
  max-width: 160px !important;
}

.app-picker-field {
  min-width: 150px !important;
  max-width: 380px !important;
}

.common-filter {
  flex-wrap: wrap;
}

.table-container {
  overflow-x: auto;
}

.table-container table {
  width: 100%;
  min-width: 800px;
  /* Adjust this to fit your table's minimum width */
}

.table-container th,
.table-container td {
  white-space: nowrap;
  /* Prevents content from wrapping and maintains a single line */
}
</style>
