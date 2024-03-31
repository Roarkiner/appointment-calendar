interface ServiceTypeSelectOptionAtomProps {
    id: number;
    name: string;
    duration: string;
}
  
const ServiceTypeSelectOptionAtom: React.FC<ServiceTypeSelectOptionAtomProps> = ({ id, name, duration }) => {
    return <option value={id}>{`${name} (${duration})`}</option>;
};

export default ServiceTypeSelectOptionAtom;