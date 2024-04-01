import "../../../assets/style/modal.css";

import React, { useState } from "react";
import "react-datepicker/dist/react-datepicker.css";
import { useAuth } from "../../../contexts/AuthContext";
import Modal from "react-modal";
import { toast } from "react-toastify";
import { formatDate } from "../../../services/DateHelper";
import AddSlotForm from "../../molecules/calendar/AddSlotForm";
import { saveSlot } from "../../../services/SlotService";

interface AddSlotModalProps {
    isModalOpen: boolean;
    closeModal: () => void;
    onSlotCreated: () => void
}

Modal.setAppElement('#root');

const AddSlotModal: React.FC<AddSlotModalProps> = ({ isModalOpen, closeModal, onSlotCreated }) => {
    const { isAdmin } = useAuth();
    const [selectedStartDate, setSelectedStartDate] = useState<Date | null>(new Date());
    const [selectedEndDate, setSelectedEndDate] = useState<Date | null>(new Date());
    const [isLoading, setIsLoading] = useState(false);

    const trySaveSlot = async (): Promise<void> => {
        setIsLoading(true);

        if (selectedStartDate == null || selectedEndDate == null) {
            toast.error("Veuillez sélectionner une date", {
                position:'bottom-right',
                autoClose: 3000,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: false
            });
            setIsLoading(false);
            return;
        }

        const slotSaveModel: SlotSaveModel = {
            start_date: formatDate(selectedStartDate!),
            end_date: formatDate(selectedEndDate!)
        }

        try {
            const saveSlotResponse = await saveSlot(slotSaveModel);
            
            setIsLoading(false);
    
            if (saveSlotResponse.status == 201) {
                toast.success("Le créneau a été ajouté!", {
                    position:'bottom-right',
                    autoClose: 3000,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: false
                });
            }

            onSlotCreated();
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
            contentLabel="Ajouter un créneau"
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
            contentLabel="Ajouter un créneau"
            className="Modal"
            overlayClassName="Overlay"
        >
            <AddSlotForm selectedStartDate={selectedStartDate}
                setSelectedStartDate={setSelectedStartDate}
                selectedEndDate={selectedEndDate}
                setSelectedEndDate={setSelectedEndDate}
                onFormCompletion={trySaveSlot} />
      </Modal>
  );
};

export default AddSlotModal;
