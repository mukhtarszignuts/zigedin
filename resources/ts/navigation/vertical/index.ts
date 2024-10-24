import type { VerticalNavItems } from '@/@layouts/types';


const adminNavItems: VerticalNavItems = [
  {
    title: "Dashboard",
    to: { name: "admin-dashboard" },
    icon: { icon: "tabler-smart-home" },
  },
  {
    title: "Users",
    to: { name: "admin-user-list" },
    icon: { icon: "tabler-users" },
  },
  // {
  //   title: "Chats",
  //   to: { name: "chat" },
  //   icon: { icon: "tabler-message-circle" },
  // },
];

const managerNavItems: VerticalNavItems = [
  {
    title: "Dashboard",
    to: { name: "admin-dashboard" },
    icon: { icon: "tabler-smart-home" },
  },
  
  // {
  //   title: "Chats",
  //   to: { name: "chat" },
  //   icon: { icon: "tabler-message-circle" },
  // },
  // other manager routes
];

const customerNavItems: VerticalNavItems = [
  {
    title: "Dashboard",
    to: { name: "index" },
    icon: { icon: "tabler-smart-home" },
  },

  // other customer routes
];

export { adminNavItems, customerNavItems, managerNavItems };

// export default [
//   {
//     title: 'Home',
//     to: { name: 'index' },
//     icon: { icon: 'tabler-smart-home' },
//   },
//   {
//     title: 'Second page',
//     to: { name: 'second-page' },
//     icon: { icon: 'tabler-file' },
//   },
// ] as VerticalNavItems
