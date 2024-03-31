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

interface Slot {
    id: number;
    start_date: Date;
    end_date: Date;
}