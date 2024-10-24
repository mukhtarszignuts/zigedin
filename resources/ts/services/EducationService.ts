import $http from "@/plugins/axios";
import { toast } from "vue3-toastify";

interface EducationRequest{
  id?: number
  school_name: string
  image_url: string
  start_date: string
  end_date: string
  degree: string
  field_of_study: string
  description: string
}


export const AddEditEducation = async (params:EducationRequest , isUpdate:boolean) => {
  try {
    
    if(isUpdate){
      const {
        data: { data },
      } = await $http.post('education/update',params);
      return data;
    }else{

      const {
        data: { data },
      } = await $http.post('education/create',params);

      return data;
    }
  } catch (e) {
    
    console.error("Error AddEdit Education :", e);
    return null;
  }
};

export const deleteEducation = async (Id:number) => {
  try {
    const data = await $http.get(`education/delete/${Id}`);
    return data;
  } catch (e) {
    console.error("Error Delete Education :", e);
    return null;
  }
};
