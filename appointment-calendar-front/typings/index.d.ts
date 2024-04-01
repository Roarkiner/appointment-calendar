declare interface LightUser {
    userId: number,
    email: string,
    username: string
}

declare interface TimeSlot {
    id: number;
    day: WeekDays;
    type: TimeSlotType;
    start: number;
    end: number;
    serviceType?: string;
}

interface ServiceType {
    id: number;
    name: string;
    duration: string;
}

interface ServiceTypeSaveModel {
    name: string;
    duration: number;
}

interface User {
    id: number;
    email: string;
    lastname: string;
    firstname: string;
}

interface Appointment {
    id: number;
    start_date: Date;
    end_date: Date;
    service_type: ServiceType;
    user: User;
}

interface AppointmentSaveModel {
    start_date: string;
    end_date: string;
    service_type_id: number;
    user_id: number;
}

interface Slot {
    id: number;
    start_date: Date;
    end_date: Date;
}

interface SlotSaveModel {
    start_date: string;
    end_date: string;
}

interface UserInfoResponseModel {
    id: number,
    email: string,
    lastname: string,
    firstname: string
}