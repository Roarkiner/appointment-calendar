import { UserSaveModel } from "../models/UserSaveModel";
import { api } from "./ApiService";

const registerPath = "/api/register";

export async function saveUser(userToSave: UserSaveModel): Promise<string> {
    const saveUserResponse = await api.post(registerPath, userToSave)
    .catch(error => {
        throw error;
    });
    const data = saveUserResponse.data;

    return data.token;
}