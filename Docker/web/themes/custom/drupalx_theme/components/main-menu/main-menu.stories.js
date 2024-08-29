import './main-menu.scss';
import MainMenuTemplate from './main-menu.twig';

export default {
  title: 'Navigation/Main Menu',
  argTypes: {
    items: {
      description: 'Define the array of main menu items',
      control: 'array',
      type: {
        required: true
      }
    }
  }
};

export const MainNavigation = MainMenuTemplate.bind({});

MainNavigation.args = {
  modifier: '',
  link_modifier: 'text-light',
  site_logo: './images/logo.svg',
  menu_items: [
    {
      title: 'Home',
      url: '#',
      in_active_trail: true
    },
    {
      title: 'About',
      url: '#',
      is_expanded: true,
      in_active_trail: false,
      below: [
        {
          title: 'Vestibulum ac diam',
          url: '#',
          in_active_trail: false
        },
        {
          title: 'Mauris blandit aliquet',
          url: '#',
          in_active_trail: false
        },
        {
          title: 'Pellentesque in',
          url: '#',
          in_active_trail: false
        }
      ]
    },
    {
      title: 'Evidence Exchange',
      url: '#',
      is_expanded: true,
      in_active_trail: false,
      below: [
        {
          title: 'Vestibulum ac diam',
          url: '#',
          in_active_trail: false
        },
        {
          title: 'Mauris blandit aliquet',
          url: '#',
          in_active_trail: false
        }
      ]
    },
    {
      title: 'Evaluation Resources',
      url: '#',
      is_expanded: true,
      in_active_trail: false,
      below: [
        {
          title: 'Vestibulum ac diam',
          url: '#',
          in_active_trail: false
        },
        {
          title: 'Mauris blandit aliquet',
          url: '#',
          in_active_trail: false
        },
        {
          title: 'Mauris blandit aliquet',
          url: '#',
          in_active_trail: false
        }
      ]
    },
    {
      title: 'FAQ',
      url: '#',
      in_active_trail: false
    },
    {
      title: 'Contact',
      url: '#',
      in_active_trail: false
    }
  ]
};
