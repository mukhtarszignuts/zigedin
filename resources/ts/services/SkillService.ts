import $http from "@/plugins/axios";

interface SkillRequest{
    name:string
}
export const fetchSkillList = async () => {
  try {
    const {
      data: { data },
    } = await $http.post('skill/list');
    return data;
  } catch (e) {
    console.error("Error Fetch Skill List:", e);
    return null;
  }
};

export const AddEditSkill = async (params:any , isUpdate:boolean) => {
  try {
    
    if(isUpdate){
      const data = await $http.post('skill/update',params);
      return data;
    }else{

      const data = await $http.post('skill/create',params);

      return data;
    }
  } catch (e) {
    console.error("Error AddEdit Skill :", e);
    return null;
  }
};

export const deleteSkill = async (Id:number) => {
  try {
    const data = await $http.get(`skill/delete/${Id}`);
    return data;
  } catch (e) {
    console.error("Error Delete Skill :", e);
    return null;
  }
};
