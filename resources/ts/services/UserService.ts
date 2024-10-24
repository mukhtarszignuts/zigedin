import $http from "@/plugins/axios";

interface UserListRequest {
  sort_field: string;
  sort_order: string;
  page: number;
  per_page: number;
  search: any;
  role: string | undefined;
  start_date: string | any;
  end_date: string | any;
}

export const fetchUserList = async (params: UserListRequest) => {
  try {
    const {
      data: { data },
    } = await $http.post("admin/user/list", params);
    return data;
  } catch (e) {
    console.error("Error fetching user list:", e);
    return null;
  }
};

export const getUserdata = async (id:number) => {
  try {
    const {
      data: { data },
    } = await $http.get(`admin/user/${id}`)
    return data;
  } catch (e) {
    console.error("Error get user data:", e);
    return null;
  }
};

export const deleteUser = async (id: any) => {
  try {
    const path = `admin/user/delete/${id}`;
    const { data } = await $http.get(`${path}`);
    return data;
  } catch (e) {
    console.error("Error deleting user list:", e);
    return null;
  }
};

export const AddEditUser = async (formData: any, isUpdate: boolean) => {
  try {
    if (isUpdate) {
      const  { data } = await $http.post("admin/user/update", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      return data;
    } else {
      const { data } = await $http.post("admin/user/create", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      return data;
    }
  } catch (e) {
    console.error("Error update user list:", e);
    return null;
  }
};
