import './card-group.scss';
import CardGroupTemplate from './card-group.twig';

export default {
  title: 'Editorial/Card Group',
  argTypes: {
    section_title: {
      description: 'The title of the card list component',
      control: 'text'
    },
    section_subtitle: {
      description: 'The subtitle of the card list component',
      control: 'text'
    },
    card_items: {
      description: 'Array of the card list item content',
      control: 'object',
      type: {
        required: true
      }
    }
  }
};

export const FlagCardGroup = CardGroupTemplate.bind({});

FlagCardGroup.args = {
  section_title: 'How to use The Learning Campus in a few steps:',
  section_subtitle: 'Browse our featured content, focused on delivering world-class training.',
  background_color: 'grey',
  card_items: [
    {
      card: {
        modifier: 'flag-card col-md-12 col-lg-6 mb-4 mb-lg-0',
        heading: {
          title: 'Scaler',
          heading_level: '2',
          url: '',
          modifier: 'card-title fw-normal fs-4 mb-5'
        },
        summary_text:
          'Do you want to make an impact in your community and your country? AmeriCorps members and AmeriCorps Seniors volunteers serve with organizations to strengthen communities across our nation.',
        link: {
          url: '/scaler',
          text: 'Find out more'
        }
      }
    },
    {
      card: {
        modifier: 'flag-card col-md-12 col-lg-6 mb-4 mb-lg-0',
        heading: {
          title: 'Partner',
          heading_level: '2',
          url: '',
          modifier: 'card-title fw-normal fs-4 mb-5'
        },
        summary_text:
          'In need of resources? AmeriCorps is here for you. Every year, we place more than $800 million in funding and more than 200,000 individuals with nonprofit, faith-based, and community organizations.',
        link: {
          url: '/partner',
          text: 'Find out more'
        }
      }
    }
  ]
};

export const ImageCardGroup = CardGroupTemplate.bind({});

ImageCardGroup.args = {
  section_title: 'The Learning Campus Featured Content',
  section_subtitle: 'Browse our featured content, focused on delivering world-class training',
  background_color: 'white',
  card_items: [
    {
      card: {
        modifier: 'image-card col-12 mb-4 col-lg-4 mb-lg-0',
        media:
          "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
        heading: {
          title: 'Phasellus auctor turpis',
          heading_level: '2',
          url: '#',
          modifier: 'card-title mb-4'
        },
        summary_text:
          'This copy is optional, if nothing is entered nothing will display. Facit nulla in vulputate vulputate aliquam. Commodo esse habent tation nam. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sed orci lacus.',
        type: 'Training'
      }
    },
    {
      card: {
        modifier: 'image-card col-12 mb-4 col-lg-4 mb-lg-0',
        media:
          "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
        heading: {
          title: 'Phasellus auctor turpis',
          heading_level: '2',
          url: '#',
          modifier: 'card-title mb-4'
        },
        summary_text:
          'This copy is optional, if nothing is entered nothing will display. Facit nulla in vulputate vulputate aliquam. Commodo esse habent tation nam. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sed orci lacus.',
        type: 'Video'
      }
    },
    {
      card: {
        modifier: 'image-card col-12 mb-4 col-lg-4 mb-lg-0',
        media:
          "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
        heading: {
          title: 'Phasellus auctor turpis',
          heading_level: '2',
          url: '#',
          modifier: 'card-title mb-4'
        },
        summary_text:
          'This copy is optional, if nothing is entered nothing will display. Facit nulla in vulputate vulputate aliquam. Commodo esse habent tation nam. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sed orci lacus.',
        type: 'Training'
      }
    }
  ]
};

export const NodeCountCardGroup = CardGroupTemplate.bind({});

NodeCountCardGroup.args = {
  section_title: 'Explore The Learning Campus',
  section_subtitle: 'Choose one of the items below to browse our wide range of topics and content.',
  background_color: 'blue',
  card_items: [
    {
      card: {
        modifier: 'node-count-card col-sm-8 col-md-6 col-lg-3 mb-4 mx-sm-auto mb-lg-0',
        media:
          "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
        heading: {
          title: 'Evidence Report',
          heading_level: '2',
          url: '#',
          modifier: 'card-title'
        },
        node_count: 310
      }
    },
    {
      card: {
        modifier: 'node-count-card col-sm-8 col-md-6 col-lg-3 mb-4 mx-sm-auto mb-lg-0',
        media:
          "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
        heading: {
          title: 'Webinars',
          heading_level: '2',
          url: '#',
          modifier: 'card-title'
        },
        node_count: 38
      }
    },
    {
      card: {
        modifier: 'node-count-card col-sm-8 col-md-6 col-lg-3 mb-4 mx-sm-auto mb-lg-0',
        media:
          "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
        heading: {
          title: 'Coaching',
          heading_level: '2',
          url: '#',
          modifier: 'card-title'
        },
        node_count: 70
      }
    },
    {
      card: {
        modifier: 'node-count-card col-sm-8 col-md-6 col-lg-3 mb-4 mx-sm-auto mb-lg-0',
        media:
          "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
        heading: {
          title: 'Leadership',
          heading_level: '2',
          url: '#',
          modifier: 'card-title'
        },
        node_count: 39
      }
    },
    {
      card: {
        modifier: 'node-count-card col-sm-8 col-md-6 col-lg-3 mb-4 mx-sm-auto mb-lg-0',
        media:
          "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
        heading: {
          title: 'Diversity',
          heading_level: '2',
          url: '#',
          modifier: 'card-title'
        },
        node_count: 26
      }
    },
    {
      card: {
        modifier: 'node-count-card col-sm-8 col-md-6 col-lg-3 mb-4 mx-sm-auto mb-lg-0',
        media:
          "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
        heading: {
          title: 'Safety',
          heading_level: '2',
          url: '#',
          modifier: 'card-title'
        },
        node_count: 17
      }
    }
  ]
};

export const StatsCardGroup = CardGroupTemplate.bind({});

StatsCardGroup.args = {
  section_title: 'Top Stats',
  section_subtitle: 'Browse our featured content, focused on delivering world-class training',
  background_color: 'grey',
  card_items: [
    {
      media:
        '<span class="material-symbols-outlined display-1">monitoring</span>',
      heading: 'This is small headling',
      body: 'Vestibulum fringilla pede sit amet augue. Nullam nulla eros, ultricies sit amet, nonummy id, imperdiet feugiat, pede.',
      modifier: 'stat-card'
    },
    {
      media:
        '<span class="material-symbols-outlined display-1">bar_chart_4_bars</span>',
      heading: 'This is small headling',
      body: 'Vestibulum fringilla pede sit amet augue. Nullam nulla eros, ultricies sit amet, nonummy id, imperdiet feugiat, pede.',
      modifier: 'stat-card'
    },
    {
      media:
        '<span class="material-symbols-outlined display-1">grouped_bar_chart</span>',
      heading: 'This is small headling',
      body: 'Vestibulum fringilla pede sit amet augue. Nullam nulla eros, ultricies sit amet, nonummy id, imperdiet feugiat, pede.',
      modifier: 'stat-card'
    }
  ]
};
