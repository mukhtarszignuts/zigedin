<script setup lang="ts">
import CardStatisticsVertical from "@/components/CardStatisticsVertical.vue";
import useUserData from "@/composable/useFetchUserData";
import { useHead } from "@vueuse/head";

// import moment from "moment";
import axios from "@/plugins/axios";

interface DashboardItem {
  title: string;
  color: string;
  stats: string;
  icon: string;
}

const { fetchUserData } = useUserData();

const dashboardData = ref<DashboardItem[]>([]);
const isLoading = ref<boolean>(false);

const role = ref<string>("");

const getDashboardDetails = async () => {
  try {
    const { data } = await axios.get("admin/dashboard");
    
    
    if (data) {
      dashboardData.value = [
        {
          title: "User",
          color: "info",
          stats: data.data.users,
          icon: "tabler-users",
        },
      ];
    }
  } catch (e) {
    console.error(e);
  }
};

onMounted(() => {
  const userData = fetchUserData();
  if (userData) {
    role.value = userData.role;
    // if (userData.role === "M") {
    //   fetchCompanyData(userData.company_id);
    // }
  }
  getDashboardDetails();
 
});

useHead({
  title: "zigedin | Dashboard",
});
</script>
<template>
    <div v-if="role === 'A'">
    <VRow>
      <VCol
        v-for="statistics in dashboardData"
        :key="statistics.title"
        md="2"
        cols="12"
      >
        <CardStatisticsVertical v-bind="statistics" />
      </VCol>
    </VRow>
  </div>
  </template>
  