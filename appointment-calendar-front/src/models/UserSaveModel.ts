export class UserSaveModel {
    public email: string;
    public password: string;
    public firstname: string;
    public lastname: string;

    constructor(email: string, password: string, lastname: string, firstname: string) {
        this.email = email;
        this.password = password;
        this.lastname = lastname;
        this.firstname = firstname;
    }
}