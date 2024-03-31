import React from "react";

interface TimeSlotAtomProps {
    height: number;
    backgroundColor: string;
    topPosition: number;
    zIndex: number;
    label?: string;
}

const TimeSlotAtom: React.FC<TimeSlotAtomProps> = ({ height, backgroundColor, topPosition, zIndex, label }) => (
    <div className="time-slot" style={{
        height: `${height}px`,
        backgroundColor,
        position: 'absolute',
        top: `${topPosition}px`,
        zIndex
    }}>
        { label }
    </div>
);

export default TimeSlotAtom;