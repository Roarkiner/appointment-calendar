import React from "react";
import ButtonAtom from "../../atoms/shared/ButtonAtom";
import InputAtom from "../../atoms/shared/InputAtom";

interface AddServiceTypeFormProps {
    selectedName: string;
    setSelectedName: (name: string) => void;
    selectedDuration: number;
    setSelectedDuration: (duration: number) => void;
    onFormCompletion: () => void;
}

const AddServiceTypeForm: React.FC<AddServiceTypeFormProps> = ({ 
    selectedName, 
    setSelectedName, 
    selectedDuration, 
    setSelectedDuration, 
    onFormCompletion 
}) => {
    const handleDurationChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = Math.max(15, parseInt(e.target.value, 10));
        setSelectedDuration(value);
    };

    return (
        <div className="d-flex flex-column px-3">
            <h2 className="mb-3">Ajouter une prestation</h2>

            <InputAtom 
                id="serviceName" 
                className="mb-3 d-flex" 
                label="Nom de la prestation :" 
                onChange={(e) => setSelectedName(e.target.value)} 
                type="text" 
                value={selectedName}
            />
            
            <InputAtom 
                id="serviceDuration" 
                className="mb-3 mt-3 d-flex" 
                label="Durée de la prestation :" 
                onChange={handleDurationChange} 
                type="number" 
                value={selectedDuration}
                min="15"
                step="15"
            />

            <ButtonAtom className="confirm-service-type mt-5" onClick={onFormCompletion}>Ajouter la préstation</ButtonAtom>
        </div>
    );
}

export default AddServiceTypeForm;