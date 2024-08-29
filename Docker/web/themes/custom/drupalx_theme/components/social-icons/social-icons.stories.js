import './social-icons.scss';
import SocialIconsTemplate from './social-icons.twig';

export default {
  title: 'General/Social Icons',
  argTypes: {
    social_icons: {
      description: 'Define the social icons',
      control: 'object'
    }
  }
};

export const SocialIcons = SocialIconsTemplate.bind({});

SocialIcons.args = {
  social_icons: {
    facebook_url: 'https://www.facebook.com/americorps/',
    twitter_url: 'https://twitter.com/americorps?lang=en',
    instagram_url: 'https://www.instagram.com/americorps/?hl=en',
    youtube_url: 'https://www.youtube.com/channel/UC1VuXCdPlovso1vcqi2dMng',
    linkedin_url: 'https://www.linkedin.com/company/americorps',
    style: 'social-icon-light',
    modifier: 'p-3 bg-light'
  }
};

export const SocialIconsDark = SocialIconsTemplate.bind({});

SocialIconsDark.args = {
  social_icons: {
    facebook_url: 'https://www.facebook.com/americorps/',
    twitter_url: 'https://twitter.com/americorps?lang=en',
    instagram_url: 'https://www.instagram.com/americorps/?hl=en',
    youtube_url: 'https://www.youtube.com/channel/UC1VuXCdPlovso1vcqi2dMng',
    linkedin_url: 'https://www.linkedin.com/company/americorps',
    style: 'social-icon-dark',
    modifier: 'p-3 bg-dark'
  }
};
