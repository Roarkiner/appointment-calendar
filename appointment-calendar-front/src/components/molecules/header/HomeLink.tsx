import LinkAtom from '../../atoms/shared/LinkAtom';
import LogoAtom from '../../atoms/shared/LogoAtom';

const HomeLink: React.FC = () => {

  return (
    <LinkAtom to='/'>
        <LogoAtom alt='Home' src='/vite.svg' height='50px' />
    </LinkAtom>
  );
};

export default HomeLink;