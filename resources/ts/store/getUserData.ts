import { defineStore } from "pinia";
import CryptoJs from "crypto-js";
import $http from "@/plugins/axios";

export const useUserData = defineStore('userDetails',()=>{
    
    const userData = ref<any>('')

    // Methods
  const getUserDetails = async () => {
    try {
      const encryptedData = localStorage.getItem('user-data')
      const user = encryptedData ? JSON.parse(CryptoJs.AES.decrypt(encryptedData || '', import.meta.env.VITE_CRYPTO_SECURE_KEY).toString(CryptoJs.enc.Utf8)) : null
      if (!user)
        return

      const { data: { data } } = await $http.get(`/user/${user?.id}`)
      if (data)
        console.log(data.user,'store');
        
        userData.value = data.user
    }
    catch (e) {
      console.log(e)
    }
  }

  return {
    // Data
    userData,

    // Methods
    getUserDetails,
  }

})
