import React from "react";
import ColoredCircleAtom from "../../atoms/shared/ColoredCircleAtom";

const CalendarLegend: React.FC = () => {
    return (
        <div className="my-3 d-flex">
            <div className="d-flex me-3">
                <ColoredCircleAtom width={20} height={20} backgroundColor="orange"/>
                <span>Occupé</span>
            </div>
            <div className="d-flex">
                <ColoredCircleAtom width={20} height={20} backgroundColor="lightblue"/>
                <span>Disponible</span>
            </div>
        </div>
    )
}

export default CalendarLegend;