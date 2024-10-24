import useUserData from "./useFetchUserData";
const { fetchUserData } = useUserData();

export default function useFormatting() {
 
 const user = fetchUserData();

  // Define the additional role
  const additionalRoles = [
    { id: "A", name: "Admin" },
  ];

  const roles = ref<any[]>([
    {
      id: "A",
      name: "Admin",
    },
    {
      id: "C",
      name: "Candidate",
    },
    {
      id: "E",
      name: "Employer",
    },
  ]);

  // Computed property to return the updated roles based on user role
  const computedRoles = computed(() => {
    // Check user role and conditionally add the 'Admin' role
    // if (user.value?.role === "A") {
    //   return [...roles.value, ...additionalRoles];
    // }
    return roles.value;
  });

  const roleVariant = (role: string) => {
    const roleOption = computedRoles.value.find((option) => option.id === role);
    if (roleOption) {
      switch (role) {
        case "A":
          return { color: "dark", text: roleOption.name };
        case "E":
          return { color: "success", text: roleOption.name };
        case "C":
          return { color: "primary", text: roleOption.name };
        default:
          return { color: "secondary", text: "Undefined" };
      }
    }else{
      return { color: "secondary", text: "Undefined" };
    }
  };

  // user status
  const statusOptions = ref<any[]>([
    {
      id: "P",
      name: "Pending",
    },
    {
      id: "A",
      name: "Active",
    },
    {
      id: "I",
      name: "Rejected",
    },
  ]);

  const statusVariant = (status: string) => {
    const statusOption = statusOptions.value.find(
      (option) => option.id === status
    );

    if (statusOption) {
      switch (status) {
        case "A":
          return { color: "success", text: statusOption.name };
        case "P":
          return { color: "warning", text: statusOption.name };
        case "I":
          return { color: "error", text: statusOption.name };
        default:
          return { color: "secondary", text: "Undefined" };
      }
    } else {
      return { color: "secondary", text: "Undefined" };
    }
  };

  return {
    statusOptions,
    statusVariant,
    roles: computedRoles,
    roleVariant,
  };
}
