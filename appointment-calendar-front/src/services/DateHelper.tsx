import { fr } from "date-fns/locale";
import { TimeSlotType } from "../components/molecules/calendar/DayColumn";
import { WeekDays } from "../components/organisms/calendar/WeeklyCalendar";
import { format, differenceInMinutes, startOfDay } from 'date-fns';
import { capitalizeFirstLetter } from "./UtilityHelper";
import { Duration, DateTime } from 'luxon';

export const formatDate = (date: Date) => {
    const year = date.getFullYear();
    const month = (`0${date.getMonth() + 1}`).slice(-2);
    const day = (`0${date.getDate()}`).slice(-2);
    const hour = (`0${date.getHours()}`).slice(-2);
    const minute = (`0${date.getMinutes()}`).slice(-2);
    
    return `${year}-${month}-${day} ${hour}:${minute}`;
};

export function convertISODurationToReadableFormat(isoDuration: string) {
    const duration = Duration.fromISO(isoDuration);
    return `${duration.hours > 0 ? `${duration.hours}h` : ''}${duration.minutes > 0 ? `${duration.minutes}` : ''}`;
}

export const addISODurationToDate = (date: Date, isoDuration: string): Date => {
    const luxonDate = DateTime.fromJSDate(date);
    const duration = Duration.fromISO(isoDuration);
    const result = luxonDate.plus(duration);
    return result.toJSDate();
}

export const convertAppointmentsToTimeSlots = (appointments: Appointment[]): TimeSlot[] => {
    return appointments.map(appointment => {
        const boundaries = convertDatesToTimeSlotBoundaries(appointment);
        
        return {
            id: appointment.id,
            day: boundaries.day,
            type: TimeSlotType.appointment,
            start: boundaries.start,
            end: boundaries.end,
            serviceType: appointment.service_type.name,
        };
    });
};

export const convertSlotsToTimeSlots = (slots: Slot[]): TimeSlot[] => {
    return slots.map(slot => {
        const boundaries = convertDatesToTimeSlotBoundaries(slot);
        
        return {
            id: slot.id,
            day: boundaries.day,
            type: TimeSlotType.slot,
            start: boundaries.start,
            end: boundaries.end,
        };
    });
};

interface TimeSlotDatesConvertible {
    start_date: Date;
    end_date: Date;
}

interface TimeSlotDayAndBoundaries {
    day: WeekDays;
    start: number;
    end: number;
}

const convertDatesToTimeSlotBoundaries = (convertible: TimeSlotDatesConvertible): TimeSlotDayAndBoundaries => {
    const dayStart = startOfDay(convertible.start_date);
    const baseTime = new Date(dayStart).setHours(7, 0, 0, 0);
    const startTime = new Date(convertible.start_date);
    const endTime = new Date(convertible.end_date);

    return {
        day: capitalizeFirstLetter(format(startTime, 'EEEE', { locale: fr })) as WeekDays,
        start: differenceInMinutes(startTime, baseTime) / 15,
        end: differenceInMinutes(endTime, baseTime) / 15,
    };
}