import $http from "@/plugins/axios";

interface ConnectionRequest{
    connection_id:number
    status:string
}
export const fetchConnectionslist = async () => {
  try {
    const {
      data: { data },
    } = await $http.post('connections/list');
    return data;
  } catch (e) {
    console.error("Error Fetch Connection List:", e);
    return null;
  }
};

export const connected = async (connectionId:number) => {
  try {
    const params={
      connection_id:connectionId
    }
    const {
      data: { data },
    } = await $http.post('connections/create',params);
    return data;
  } catch (e) {
    console.error("Error Create Connection :", e);
    return null;
  }
};

export const connectionRequest = async (params:ConnectionRequest) => {
  try {
    const {
      data: { data },
    } = await $http.post('connections/update',params);
    return data;
  } catch (e) {
    console.error("Error Update Connection :", e);
    return null;
  }
};

export const suggestedConnection = async () => {
  try {
    const {
      data: { data },
    } = await $http.post('connections/suggest');
    return data;
  } catch (e) {
    console.error("Error Suggested Connection :", e);
    return null;
  }
};

export const disconnected = async (connectionId:number) => {
  try {
    const {
      data: { data },
    } = await $http.get(`connections/delete/${connectionId}`);
    return data;
  } catch (e) {
    console.error("Error Delete Connection :", e);
    return null;
  }
};
