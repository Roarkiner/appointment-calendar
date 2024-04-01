import React from "react";
import ButtonAtom from "../../atoms/shared/ButtonAtom";
import { fr } from "date-fns/locale";
import ReactDatePicker from "react-datepicker";
import ServiceTypeSelect from "./ServiceTypeSelect";

interface AddAppointmentFormProps {
    selectedDate: Date | null;
    setSelectedDate: (date: Date) => void;
    selectedServiceType: string;
    setSelectedServiceType: (serviceType: string) => void;
    serviceTypeList: ServiceType[],
    onFormCompletion: () => void;
}

const AddAppointmentForm: React.FC<AddAppointmentFormProps> = ({ selectedDate, setSelectedDate, selectedServiceType, setSelectedServiceType, serviceTypeList, onFormCompletion }) => {

    return (
        <div className="d-flex flex-column">
            <h2 className="mb-4">Prendre un Rendez-vous</h2>
            <ServiceTypeSelect serviceTypes={serviceTypeList} selectedServiceType={selectedServiceType} setSelectedServiceType={setSelectedServiceType} />
            
            <label>Heure du rendez-vous:</label>
            <ReactDatePicker
                selected={selectedDate}
                onChange={(date: Date) => setSelectedDate(date)}
                showTimeSelect
                dateFormat="Pp"
                locale={fr}
                timeIntervals={15}
                minDate={new Date()}
            />
            <ButtonAtom className="confirm-appointment mt-5" onClick={onFormCompletion}>Confirmer le rendez-vous</ButtonAtom>
        </div>
    );
}

export default AddAppointmentForm;