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