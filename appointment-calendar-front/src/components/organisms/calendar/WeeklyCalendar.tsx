import "../../../assets/style/calendar.css"

import { useState, useEffect } from 'react';
import DayColumn from '../../molecules/calendar/DayColumn';
import ButtonAtom from '../../atoms/shared/ButtonAtom';
import { startOfWeek, endOfWeek, addWeeks, format, isBefore, startOfDay, setHours, setMinutes } from 'date-fns';
import React from 'react';
import { IoChevronBack, IoChevronForward } from 'react-icons/io5';
import { TimeSlotType } from '../../molecules/calendar/DayColumn';
import HourScale from '../../molecules/calendar/HourScale';
import { getAllAppointmentsBetween } from '../../../services/AppointmentService';
import CalendarLegend from "../../molecules/calendar/CalendarLegend";
import { convertAppointmentsToTimeSlots } from "../../../services/DateHelper";

export enum WeekDays {
    monday = "Lundi",
    tuesday = "Mardi",
    wednesday = "Mercredi",
    thursday = "Jeudi",
    friday = "Vendredi",
    saturday = "Samedi",
    sunday = "Dimanche"
}

const WeeklyCalendar: React.FC = () => {
    const QUARTER_HOUR_HEIGHT = 15;
    const [currentDate, setCurrentDate] = useState(new Date());
    const [appointments, setAppointments] = useState<TimeSlot[]>([]);
    const [loading, setLoading] = useState(true);

    const fetchAppointments = async () => {
        setLoading(true);
        
        const startOfCurrentWeek = startOfWeek(currentDate, { weekStartsOn: 1 });
        const endOfCurrentWeek = endOfWeek(currentDate, { weekStartsOn: 1 });

        const startDate = setHours(startOfCurrentWeek, 7);
        let endDate = setHours(endOfCurrentWeek, 19);
        endDate = setMinutes(endDate, 0);

        const fetchedAppointments = await getAllAppointmentsBetween(startDate, endDate);
        const convertedAppointments = convertAppointmentsToTimeSlots(fetchedAppointments);
        setAppointments(convertedAppointments);
        setLoading(false);
    };

    useEffect(() => {
        fetchAppointments();
    }, [currentDate]);

    const today = startOfDay(new Date());
    const startOfCurrentWeek = startOfWeek(currentDate, { weekStartsOn: 1 });
    const endOfCurrentWeek = endOfWeek(currentDate, { weekStartsOn: 1 });

    const goToPreviousWeek = () => {
        setCurrentDate(prevDate => addWeeks(prevDate, -1));
    };

    const goToNextWeek = () => {
        setCurrentDate(prevDate => addWeeks(prevDate, 1));
    };

    const slots: TimeSlot[] = [
        { id: 1, day: WeekDays.monday, type: TimeSlotType.slot, start: 0, end: 20 },
        { id: 2, day: WeekDays.wednesday, type: TimeSlotType.slot, start: 28, end: 44 },
    ]

    if (loading) return <div>Chargement...</div>;

    return (
        <div>
            <div className='d-flex justify-content-between'>
                <h2>Semaine du {format(startOfCurrentWeek, 'PPP')} au {format(endOfCurrentWeek, 'PPP')}</h2>
                <div>
                    <ButtonAtom className='previous-week' onClick={goToPreviousWeek} disabled={isBefore(startOfCurrentWeek, today)}><IoChevronBack /></ButtonAtom>
                    <ButtonAtom className='next-week' onClick={goToNextWeek}><IoChevronForward /></ButtonAtom>
                </div>
            </div>
            <CalendarLegend />
            <div className='d-flex calendar-container'>
                <div className='position-relative'>
                    <HourScale quarterHourHeight={QUARTER_HOUR_HEIGHT}/>
                </div>
                <div className="ms-5 d-flex day-container">
                    {[WeekDays.monday, WeekDays.tuesday, WeekDays.wednesday, WeekDays.thursday, WeekDays.friday, WeekDays.saturday, WeekDays.sunday].map(day => (
                        <DayColumn
                            key={day} 
                            day={day}
                            quarterHourHeight={QUARTER_HOUR_HEIGHT}
                            appointments={appointments.filter(appointment => appointment.day === day)} 
                            slots={slots.filter(appointment => appointment.day === day)}/>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default WeeklyCalendar;