import { UserSaveModel } from "../models/UserSaveModel";
import { api } from "./ApiService";

const registerPath = "/api/register";
const getUserInfoPath = "/api/user/current";

export async function saveUser(userToSave: UserSaveModel): Promise<string> {
    const saveUserResponse = await api.post(registerPath, userToSave)
    .catch(error => {
        throw error;
    });
    const data = saveUserResponse.data;

    return data.token;
}

export async function getCurrentUserInfo(): Promise<UserInfoResponseModel> {
    const currentUserInfoResponse = await api.get(getUserInfoPath);

    const currentUserInfo = {
        ...currentUserInfoResponse.data
    }

    return currentUserInfo;
}