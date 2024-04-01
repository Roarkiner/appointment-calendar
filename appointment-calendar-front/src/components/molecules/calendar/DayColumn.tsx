import "../../../assets/style/day-column.css";

import React from 'react';
import TimeSlotAtom from '../../atoms/calendar/TimeSlotAtom';
import { WeekDays } from "../../organisms/calendar/WeeklyCalendar";

interface DayColumnProps {
    day: string;
    appointments: TimeSlot[];
    slots: TimeSlot[];
    quarterHourHeight: number;
}

export enum TimeSlotType {
    appointment,
    slot
}

const DayColumn: React.FC<DayColumnProps> = ({ day, appointments, slots, quarterHourHeight }) => {

    const renderSlot = (slot: TimeSlot, zIndex: number) => {
        const height = quarterHourHeight * ((slot.end - slot.start));
        const topPosition = quarterHourHeight * slot.start;
        const backgroundColor = slot.type === TimeSlotType.appointment ? 'orange' : 'lightblue';

        return (
            <TimeSlotAtom
                key={slot.id} 
                backgroundColor={backgroundColor} 
                height={height} 
                topPosition={topPosition} 
                zIndex={zIndex}
                label={slot.serviceType}/>
        );
    };

    return (
        <div className="day-column">
            <h3 className="border-bottom border-black pb-3 mb-0 text-center">{day}</h3>
            <div className={`slots-container border-end border-black ${day == WeekDays.monday ? "border-start" : ""}`} 
                style={{ 
                    position: 'relative', 
                    height: `${quarterHourHeight * 48}px`, 
                    backgroundSize: `100% ${quarterHourHeight * 4}px` 
            }}>
                {slots.map(slot => renderSlot(slot, 1))}
                {appointments.map(appointment => renderSlot(appointment, 2))}
            </div>
        </div>
    );
};

export default DayColumn;