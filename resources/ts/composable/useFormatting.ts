import useUserData from "./useFetchUserData";
const { fetchUserData } = useUserData();


interface DateRange {
  start_date: string;
  end_date?: string;
}

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

  // location type
  const localtionTypeOptions = ref<any[]>([
    {
      id: "OS",
      name: "On Site",
    },
    {
      id: "RMT",
      name: "Remote",
    },
    {
      id: "HYB",
      name: "Hybride",
    },
  ]);

  // location type variant
  const locationTypeVariant = (type:string) =>{
    const localtionTypeOption = localtionTypeOptions.value.find(
      (option) => option.id === type
    );

    if (localtionTypeOption) {
      switch (type) {
        case "OS":
          return { color: "success", text: localtionTypeOption.name };
        case "RMT":
          return { color: "warning", text: localtionTypeOption.name };
        case "HYB":
          return { color: "error", text: localtionTypeOption.name };
        default:
          return { color: "secondary", text: "Undefined" };
      }
    } else {
      return { color: "secondary", text: "Undefined" };
    }
  }

  // employment type
  const empOptions = ref<any[]>([
    {
      id: "F",
      name: "Full-Time",
    },
    {
      id: "P",
      name: "Part-Time",
    },
    {
      id: "FL",
      name: "Freelancers",
    },
    {
      id: "SE",
      name: "Self-Employed",
    },
    {
      id: "I",
      name: "Intership",
    },
    {
      id: "T",
      name: "Trainee",
    },
  ]);

   // location type variant
   const empOptionsVariant = (type:string) =>{
    const empOption = empOptions.value.find(
      (option) => option.id === type
    );

    if (empOption) {
      switch (type) {
        case "F":
          return { color: "success", text: empOption.name };
        case "P":
          return { color: "warning", text: empOption.name };
        case "FL":
          return { color: "error", text: empOption.name };
        case "SE":
          return { color: "success", text: empOption.name };
        case "I":
          return { color: "warning", text: empOption.name };
        case "T":
          return { color: "error", text: empOption.name };
        default:
          return { color: "secondary", text: "Undefined" };
      }
    } else {
      return { color: "secondary", text: "Undefined" };
    }
  }

  const formatDateRange = ({ start_date, end_date }: DateRange): string => {
    const start = new Date(start_date);
    const end = end_date ? new Date(end_date) : new Date();
  
    const monthDiff = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
    const years = Math.floor(monthDiff / 12);
    const months = monthDiff % 12;
  
    const formattedStart = start.toLocaleString('en-US', { month: 'short', year: 'numeric' });
    const formattedEnd = end_date ? end.toLocaleString('en-US', { month: 'short', year: 'numeric' }) : 'Present';
  
    return `${formattedStart} - ${formattedEnd} ${years > 0 ? `${years} yr ` : ''}${months > 0 ? `${months} mos` : ''}`;
  }

  

  return {
    statusOptions,
    statusVariant,
    roles: computedRoles,
    roleVariant,
    formatDateRange,
    localtionTypeOptions,
    locationTypeVariant,
    empOptions,
    empOptionsVariant,
  };
}
