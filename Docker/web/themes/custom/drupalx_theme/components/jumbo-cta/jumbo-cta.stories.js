import './jumbo-cta.scss';
import JumboCTATemplate from './jumbo-cta.twig';

export default {
  title: 'Editorial/Jumbo CTA',
  argTypes: {
    title: {
      name: 'Title',
      description: 'Jumbo-CTA title',
      control: 'text'
    },
    media: {
      name: 'Media',
      description: 'Jumbo-CTA image or video markup',
      control: 'text'
    },
    media_layout: {
      name: 'Layout',
      description: 'Controls image left/right placement.',
      control: 'select',
      options: {
        'Pin image left': 'left',
        'Pin image right': 'right'
      }
    },
    layout_modifier: {
      name: 'Modifier',
      description: 'Additional classes for the component.',
      control: 'text'
    },
    body: {
      name: 'Body Text',
      description: 'Jumbo-CTA body text',
      control: 'text'
    },
    link: {
      url: '#',
      text: 'Find Out More'
    }
  }
};

export const CTA_left = JumboCTATemplate.bind({});
CTA_left.args = {
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  summary: '<p>Proactively fabricate worldwide infrastructures before top-line meta-services. Professionally expedite quality methodologies with mission-critical web-readiness. Objectively syndicate robust communities before visionary e-markets. Globally benchmark leading-edge technologies through go forward relationships. Competently scale client-focused human capital and client-centric testing procedures.</p><p>Globally iterate interactive models before open-source results. Professionally recaptiualize functional imperatives through transparent manufactured products. Distinctively strategize corporate networks for enabled materials. Conveniently create cross-unit web-readiness and client-based.</p>',
  media_layout: 'left',
  layout_modifier: 'cta grey px-4 py-8 px-md-12 py-md-8',
  link: {
    url: '#',
    text: 'Learn More About Our SCALER'
  }
};

export const CTA_Right = JumboCTATemplate.bind({});
CTA_Right.args = {
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  summary: '<p>Proactively fabricate worldwide infrastructures before top-line meta-services. Professionally expedite quality methodologies with mission-critical web-readiness. Objectively syndicate robust communities before visionary e-markets. Globally benchmark leading-edge technologies through go forward relationships. Competently scale client-focused human capital and client-centric testing procedures.</p><p>Globally iterate interactive models before open-source results. Professionally recaptiualize functional imperatives through transparent manufactured products. Distinctively strategize corporate networks for enabled materials. Conveniently create cross-unit web-readiness and client-based.</p>',
  media_layout: 'right',
  layout_modifier: 'cta grey px-4 py-8 px-md-12 py-md-8',
  link: {
    url: '#',
    text: 'Learn More About Our SCALER'
  }
};
