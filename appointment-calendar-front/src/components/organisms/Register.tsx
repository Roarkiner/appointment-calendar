import { useState } from "react";
import { registerUser } from "../../services/AuthService";
import { UserSaveModel } from "../../models/UserSaveModel";
import { displayCustomToastError, displayDefaultToastError } from "../../services/ToastHelper";
import { AxiosError } from "axios";
import RegistrationForm from '../molecules/Auth/RegistrationForm';
import React from "react";

const Register: React.FC = () => {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [lastname, setLastname] = useState("");
    const [firstname, setFirstname] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");
    const [isLoading, setIsLoading] = useState(false);
    const [errors, setErrors] = useState({
        email: false,
        password: false,
        confirmPassword: false,
        lastname: false,
        firstname: false
    });

    function validateEmail(): boolean {
        const emailRegex = /^\S+@\S+\.\S+$/;
        if (!emailRegex.test(email)) {
            return false;
        } else {
            return true;
        }
    }

    function validatePassword(): boolean {
        if (password.length < 8) {
            return false;
        } else {
            return true;
        }
    };

    function validateConfirmPassword(): boolean {
        if (confirmPassword !== password) {
            return false;
        } else {
            return true;
        }
    };

    function validateName(name: string): boolean {
        return name.trim().length > 0;
    }

    function validateInputsThenSetErrors(): boolean {
        const isEmailCorrect = validateEmail();
        const isPasswordCorrect = validatePassword();
        const isConfirmPasswordCorrect = validateConfirmPassword();
        const isLastnameCorrect = validateName(lastname);
        const isFirstnameCorrect = validateName(firstname);

        setErrors({
            email: !isEmailCorrect,
            password: !isPasswordCorrect,
            confirmPassword: !isConfirmPasswordCorrect,
            lastname: !isLastnameCorrect,
            firstname: !isFirstnameCorrect
        });

        return isEmailCorrect && isPasswordCorrect && isConfirmPasswordCorrect && isLastnameCorrect && isFirstnameCorrect;
    };

    function handleEmailBlur(): void {
        setErrors({
            email: !validateEmail(),
            password: errors.password,
            confirmPassword: errors.confirmPassword,
            lastname: errors.lastname,
            firstname: errors.firstname
        });
    }

    function handlePasswordBlur(): void {
        setErrors({
            email: errors.email,
            password: !validatePassword(),
            confirmPassword: errors.confirmPassword,
            lastname: errors.lastname,
            firstname: errors.firstname
        });
    }

    function handleConfirmPasswordBlur(): void {
        setErrors({
            email: errors.email,
            password: errors.password,
            confirmPassword: !validateConfirmPassword(),
            lastname: errors.lastname,
            firstname: errors.firstname
        });
    }

    function handleLastnameBlur(): void {
        setErrors({
            email: errors.email,
            password: errors.password,
            confirmPassword: errors.confirmPassword,
            lastname: !validateName(lastname),
            firstname: errors.firstname
        });
    }

    function handleFirstnameBlur(): void {
        setErrors({
            email: errors.email,
            password: errors.password,
            confirmPassword: errors.confirmPassword,
            lastname: errors.lastname,
            firstname: !validateName(firstname)
        });
    }

    async function handleRegister(e: React.FormEvent) {
        e.preventDefault();
        setIsLoading(true);
        if (!validateInputsThenSetErrors()) {
            setIsLoading(false);
            return;
        }

        try {
            await registerUser(new UserSaveModel(email, password, lastname, firstname));
            // window.location.href = "/";
        } catch (error) {
            if (error instanceof AxiosError) {
                displayCustomToastError(error.response?.data.message);
            } else {
                displayDefaultToastError();
            }
            setIsLoading(false);
        }
    };

    return (
        <div className="register-form">
            <h2>Créer un compte</h2>
            <RegistrationForm
                email={email}
                setEmail={setEmail}
                handleEmailBlur={handleEmailBlur}
                password={password}
                setPassword={setPassword}
                handlePasswordBlur={handlePasswordBlur}
                confirmPassword={confirmPassword}
                setConfirmPassword={setConfirmPassword}
                handleConfirmPasswordBlur={handleConfirmPasswordBlur}
                errors={errors}
                isLoading={isLoading}
                handleRegister={handleRegister} 
                lastname={lastname} 
                setLastname={setLastname}
                handleLastnameBlur={handleLastnameBlur}
                firstname={firstname}
                setFirstname={setFirstname}
                handleFirstnameBlur={handleFirstnameBlur}
                />
        </div>
    );
};

export default Register;