import { jwtDecode } from "jwt-decode";
import { api } from "./ApiService";
import { UserSaveModel } from "../models/UserSaveModel";
import { saveUser } from "./UserService";
import { useAuth } from "../contexts/AuthContext";

export function isUserAuthenticated(): boolean {
    return getApiToken() !== null;
}

export function getApiToken(): string | null {
    return localStorage.getItem("apiToken");
}

export function getConnectedUserEmail(): string | null {
    const token = getApiToken();
    if (token === null)
        return null;

    const decodedToken: any = jwtDecode(token);
    return decodedToken.username;
}

export function getConnectedUserRoles(): string[] {
    const token = getApiToken();
    if (token === null)
        return [];

    const decodedToken: any = jwtDecode(token);
    return decodedToken.roles;
}

export function isConnectedUserAdmin(): boolean {
    const roles = getConnectedUserRoles();
    if (roles == null) {
        return false;
    }

    return roles.includes("ROLE_ADMIN");
}

export async function loginUser(email: string, password: string): Promise<void> {
    const loginUserResponse = await api.post("api/login_check", {
        username: email,
        password: password
    });
    const apiToken = loginUserResponse.data.token;
    console.log(apiToken);
    setApiToken(apiToken);
}

export async function registerUser(userToRegister: UserSaveModel): Promise<void> {
    const apiToken = await saveUser(userToRegister);
    console.log(apiToken);
    setApiToken(apiToken);
}

function setApiToken(apiToken: string): void{
    localStorage.setItem("apiToken", apiToken);
}

function removeApiToken(): void {
    localStorage.removeItem("apiToken");
}

export function askUserForConnection(displayError: boolean = true, redirectUrl: string = "") {
    removeApiToken();
    const queryParams = new URLSearchParams({
        error: displayError ? "true" : "",
        redirectUrl: redirectUrl
    });
    window.location.href = `/login?${queryParams}`;
}

export function disconnectUser(): void {
    removeApiToken();
    const { setIsAuthenticated, setIsAdmin } = useAuth();
    setIsAuthenticated(false);
    setIsAdmin(false);
    window.location.href = "/";
}