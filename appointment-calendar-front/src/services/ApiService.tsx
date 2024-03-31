import axios, { AxiosError, AxiosInstance, AxiosResponse } from "axios";
import { askUserForConnection, getApiToken } from "./AuthService";
import { displayCustomToastError } from "./ToastHelper";

const apiUrl = "http://localhost:8000/";

const axiosInstance: AxiosInstance = axios.create({
    baseURL: apiUrl,
    timeout: 5000
});

axiosInstance.interceptors.request.use((config) => {
    const apiToken = getApiToken();
    if (apiToken) {
        config.headers["Authorization"] = `Bearer ${apiToken}`;
        config.headers["Content-Type"] = "application/json";
    }
    return config;
});

axiosInstance.interceptors.response.use(
    (response: AxiosResponse) => {
        return response;
    },
    (error: AxiosError<any, any>) => {
        if (error.response?.status === 401) {
            if(window.location.pathname == "/login") {
                displayCustomToastError(error.response?.data.message);
                throw error;
            } else
                askUserForConnection();
        } else {
            return Promise.reject(error);
        }
    }
);

export const api = axiosInstance;