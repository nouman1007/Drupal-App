import './top-banner.scss';
import TopBannerTemplate from './top-banner.twig';

export default {
  title: 'Navigation/Top Banner',
  argTypes: {
    links: {
      description: 'Define the Top Banner links.',
      control: 'array'
    }
  }
};

export const TopBanner = TopBannerTemplate.bind({});

TopBanner.args = {
  media: '<img class="usa-banner-flag" src="./images/us_flag_small.png" alt="U.S. flag" />',
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
  ]
};
