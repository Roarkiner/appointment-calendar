import React from "react";
import ButtonAtom from "../../atoms/shared/ButtonAtom";
import { fr } from "date-fns/locale";
import ReactDatePicker from "react-datepicker";

interface AddSlotFormProps {
    selectedStartDate: Date | null;
    setSelectedStartDate: (date: Date) => void;
    selectedEndDate: Date | null;
    setSelectedEndDate: (date: Date) => void;
    onFormCompletion: () => void;
}

const AddSlotForm: React.FC<AddSlotFormProps> = ({ selectedStartDate, setSelectedStartDate, selectedEndDate, setSelectedEndDate, onFormCompletion }) => {

    return (
        <div className="d-flex flex-column px-3">
            <h2 className="mb-3">Ajouter un créneau</h2>
            <label>Début du créneau:</label>
            <ReactDatePicker
                selected={selectedStartDate}
                onChange={(date: Date) => setSelectedStartDate(date)}
                showTimeSelect
                dateFormat="Pp"
                locale={fr}
                timeIntervals={15}
                minDate={new Date()}
            />

            <label className="mt-3">Fin du créneau:</label>
            <ReactDatePicker
                selected={selectedEndDate}
                onChange={(date: Date) => setSelectedEndDate(date)}
                showTimeSelect
                dateFormat="Pp"
                locale={fr}
                timeIntervals={15}
                minDate={new Date()}
            />
            <ButtonAtom className="confirm-slot mt-5" onClick={onFormCompletion}>Ajouter le créneau</ButtonAtom>
        </div>
    );
}

export default AddSlotForm;