import './header-menu.scss';
import HeaderMenuTemplate from './header-menu.twig';

export default {
  title: 'Navigation/Header Menu',
  argTypes: {
    menu_items: {
      description: 'Define the links',
      control: 'array'
    },
    attibutes: {
      description: 'Define the attibutes',
      control: 'object'
    },
    modifier: {
      description: 'Define the modifier',
      control: 'text'
    },
    item_modifier: {
      description: 'Define the list item modifier',
      control: 'text'
    }
  }
};

export const HeaderMenu = HeaderMenuTemplate.bind({});

HeaderMenu.args = {
  menu_items: [
    {
      url: '#',
      title: 'Home'
    },
    {
      url: '#',
      title: 'About'
    },
    {
      url: '#',
      title: 'Evidence Exchange'
    },
    {
      url: '#',
      title: 'Evaluation Resources'
    },
    {
      url: '#',
      title: 'FAQ'
    },
    {
      url: '#',
      title: 'Contact'
    }
  ],
  modifier: 'd-flex align-items-center justify-content-evenly',
  item_modifier: ''
};
