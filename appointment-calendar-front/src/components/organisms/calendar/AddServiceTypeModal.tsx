import "../../../assets/style/modal.css";

import React, { useState } from "react";
import "react-datepicker/dist/react-datepicker.css";
import { useAuth } from "../../../contexts/AuthContext";
import Modal from "react-modal";
import { toast } from "react-toastify";
import AddServiceTypeForm from "../../molecules/calendar/AddServiceTypeForm";
import { saveServiceType } from "../../../services/ServiceTypeService";

interface AddServiceTypeModalProps {
    isModalOpen: boolean;
    closeModal: () => void;
    onServiceTypeCreated: () => void
}

Modal.setAppElement('#root');

const AddServiceTypeModal: React.FC<AddServiceTypeModalProps> = ({ isModalOpen, closeModal, onServiceTypeCreated}) => {
    const { isAdmin } = useAuth();
    const [selectedName, setSelectedName] = useState("");
    const [selectedDuration, setSelectedDuration] = useState(15);
    
    const [isLoading, setIsLoading] = useState(false);

    const trySaveServiceType = async (): Promise<void> => {
        setIsLoading(true);

        const serviceTypeSaveModel: ServiceTypeSaveModel = {
            name: selectedName,
            duration: selectedDuration
        }

        try {
            const saveServiceTypeResponse = await saveServiceType(serviceTypeSaveModel);
            
            setIsLoading(false);
    
            if (saveServiceTypeResponse.status == 201) {
                toast.success("Le type de prestation a été ajouté!", {
                    position:'bottom-right',
                    autoClose: 3000,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: false
                });
                onServiceTypeCreated();
            }
        } catch (e: any) {
            e.response.data.errors.forEach((errorMessage: string) => {
                toast.error(errorMessage, {
                    position:'bottom-right',
                    autoClose: 3000,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: false
                });
            });
        } finally {
            setIsLoading(false);
            closeModal();
        }
    }

    if (!isAdmin) {
        return null;
    }

    if (isLoading) return (
        <Modal
            isOpen={isModalOpen}
            onRequestClose={closeModal}
            contentLabel="Ajouter un type de prestation"
            className="Modal"
            overlayClassName="Overlay"
        >
            <div className="mt-5 text-center">Chargement...</div>
        </Modal>
    );

    return (
        <Modal
            isOpen={isModalOpen}
            onRequestClose={closeModal}
            contentLabel="Ajouter un type de prestation"
            className="Modal"
            overlayClassName="Overlay"
        >
            <AddServiceTypeForm selectedName={selectedName}
                setSelectedName={setSelectedName}
                selectedDuration={selectedDuration}
                setSelectedDuration={setSelectedDuration}
                onFormCompletion={trySaveServiceType} />
      </Modal>
  );
};

export default AddServiceTypeModal;
