import CryptoJs from "crypto-js";
import { setupLayouts } from 'virtual:generated-layouts';
import { createRouter, createWebHistory } from 'vue-router';
import routes from '~pages';


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    ...setupLayouts(routes),
  ],
})


// checks auth token
const checkToken = () => {
  return !!localStorage.getItem("auth-token");
};

router.beforeEach((to, from, next) => {
  const encryptedData = localStorage.getItem("user-data");
  const user = encryptedData
    ? JSON.parse(
        CryptoJs.AES.decrypt(
          encryptedData || "",
          import.meta.env.VITE_CRYPTO_SECURE_KEY
        ).toString(CryptoJs.enc.Utf8)
      )
    : null;

  if (
    !checkToken() &&
    to.name !== "login" &&
    to.name !== "registration" &&
    to.name !== "set-new-password" &&
    to.name !== "reset-password" &&
    to.name !== "two-factor-authentication" &&
    to.name !== "forgot-password" &&
    to.name !== "email-verification" &&
    to.name !== "admin-login"
  ) {
    const inputString = to.name;

    if (inputString?.includes("admin")) next({ name: "login" });
    else next({ name: "login" });
  } else if (checkToken()) {
    if (
      to.name === "login" ||
      to.name === "registration" ||
      to.name === "email-verification" ||
      to.name === "admin-login"
    ) {
      next({ name: user?.role === "C" ? "index" : "admin-dashboard" }); // C is for User role
    } else {
      if (user?.role === "C") {
        // C is for User role
        if (typeof to.name === "string" && to.name.startsWith("admin"))
          next({ name: "admin-dashboard" });
        else next();
      } else {
        next();
      }
    }
  } else {
    next();
  }
});

// Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards

export default router
