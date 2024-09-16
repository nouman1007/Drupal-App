import './card.scss';
import CardTemplate from './card.twig';

export default {
  title: 'General/Card',
  argTypes: {
    heading: {
      name: 'Heading',
      description: 'Heading of the card',
      control: 'object'
    }
  }
};

export const FlagCard = CardTemplate.bind({});

FlagCard.args = {
  card: {
    modifier: 'flag-card col-md-12 col-lg-6',
    heading: {
      title: 'Serve',
      heading_level: '2',
      url: '',
      modifier: 'card-title fw-normal fs-4 mb-5'
    },
    layout: '',
    summary_text:
      'Do you want to make an impact in your community and your country? AmeriCorps members and AmeriCorps Seniors volunteers serve with organizations to strengthen communities across our nation.',
    link: {
      url: '#',
      text: 'Find Out More'
    }
  }
};

export const ImageCard = CardTemplate.bind({});

ImageCard.args = {
  card: {
    modifier: 'image-card col-md-12 col-lg-4',
    media:
      "<img src='./images/card.webp' class='card-img-top' alt='test image' />",
    heading: {
      title: 'Phasellus auctor, turpis',
      heading_level: '2',
      url: '#',
      modifier: 'card-title mb-3'
    },
    layout: '',
    summary_text:
      'This copy is optional, if nothing is entered nothing will display. Facit nulla in vulputate vulputate aliquam. Commodo esse habent tation nam. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sed orci lacus.',
    type: 'Training'
  }
};

export const NodeCountCard = CardTemplate.bind({});

NodeCountCard.args = {
  card: {
    modifier: 'node-count-card col-sm-12 col-md-6 col-lg-3',
    media:
      "<img src='./images/evidence-report.webp' class='card-img-top' alt='test image' />",
    heading: {
      title: 'Evidence Report',
      heading_level: '2',
      url: '/evidence-report',
      modifier: 'card-title'
    },
    node_count: 310
  }
};