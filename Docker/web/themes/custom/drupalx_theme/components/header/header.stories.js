import './header.scss';
import HeaderTemplate from './header.twig';


export default {
  title: 'Navigation/Header',
  argTypes: {
    modifier: { control: 'text' },
    media: { control: 'text' },
    title: { control: 'text' },
    body_text: { control: 'text' },
    layout: {
      control: 'radio',
      options: ['image_top', 'image_bottom']
    },
  }
};

export const Header = HeaderTemplate.bind({});
Header.args = {
  modifier: '',
  title: 'Welcome to The Learning Campus',
  body_text:
    'Through our programs, AmeriCorps offers opportunities with different time commitments and requirements, meaning you can serve in whatever capacity works for your goals and lifestyle.',
  layout: 'image_bottom',
  endorsement: 'An official website of the United States government.',
  button_link: {
    url: '#',
    text: 'Here’s how you know'
  },
  links: [
    {
      url: '#',
      text: 'Visit AmeriCorps Website'
    },
    {
      url: '#',
      text: 'Help'
    },
  ],
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
      below: [
        {
          title: 'Vestibulum ac diam',
          url: '#'
        },
        {
          title: 'Mauris blandit aliquet',
          url: '#'
        },
        {
          title: 'Pellentesque in',
          url: '#'
        }
      ]
    },
    {
      title: 'Evidence Exchange',
      url: '#',
      is_expanded: true,
      below: [
        {
          title: 'Vestibulum ac diam',
          url: '#'
        },
        {
          title: 'Mauris blandit aliquet',
          url: '#'
        }
      ]
    },
    {
      title: 'Evaluation Resources',
      url: '#',
      is_expanded: true,
      below: [
        {
          title: 'Vestibulum ac diam',
          url: '#'
        },
        {
          title: 'Mauris blandit aliquet',
          url: '#'
        },
        {
          title: 'Mauris blandit aliquet',
          url: '#'
        }
      ]
    },
    {
      title: 'FAQ',
      url: '#'
    },
    {
      title: 'Contact',
      url: '#'
    }
  ]
};
