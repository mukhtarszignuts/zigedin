import $http from "@/plugins/axios";
import { toast } from "vue3-toastify";

interface ExperienceRequest{
    id?:number, 
    title:string, 
    start_date:string, 
    end_date?:string, 
    company_name:string, 
    description:string, 
    employment_type:string, 
    location:string, 
    location_type:string,
}


export const AddEditExperience = async (params:ExperienceRequest , isUpdate:boolean) => {
  try {
    
    if(isUpdate){
      const data = await $http.post('work/update',params);
      return data;
    }else{
      const data = await $http.post('work/create',params);
      return data;
    }
  } catch (e) {
    
    console.error("Error AddEdit Experience :", e);
    return null;
  }
};

export const deleteExperience = async (Id:number) => {
  try {
    const data = await $http.get(`work/delete/${Id}`);
    return data;
  } catch (e) {
    console.error("Error Delete Experience :", e);
    return null;
  }
};
