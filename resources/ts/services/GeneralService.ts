import $http from "@/plugins/axios";
import CryptoJs from "crypto-js";

export const fetchUserRoleList = async (role: string) => {
  try {
    const input = {
      role: role,
    };
    const {
      data: { data },
    } = await $http.post("admin/user/list", input);
    return data;
  } catch (e) {
    console.error("Error fetching user list:", e);
    return null;
  }
};

export const fetchCompanyList = async () => {
  try {
    const {
      data: { data },
    } = await $http.post('admin/company/list');
    return data;
  } catch (e) {
    console.error("Error Fetch Company List:", e);
    return null;
  }
};
