describe('CTA-Jumbo Component', () => {
  beforeEach(() => {
    cy.visit('iframe.html?args=&id=editorial-jumbo-cta--left&viewMode=story');
  });

  it('should render the Left variant correctly', () => {
    // Check for headline
    cy.get('.jumbo-cta .row').within(() => {
      cy.contains('Lorem ipsum dolor sit amet, elit.');
    });

    // Check for eyebrow
    cy.get('.badge').contains('Eyebrow');

    // Check if media image is present
    cy.get('.jumbo-cta img').should('have.attr', 'src', './images/card.webp');

    // Check for body text
    cy.contains('Contra legem facit qui id facit quod lex prohibet.');

    // Check for button
    cy.get('.jumbo-cta .btn-primary').contains('Call to action');
  });

  it('should render the Right variant correctly', () => {
    cy.visit('/iframe.html?args=&id=editorial-jumbo-cta--right&viewMode=story');

    // Check for headline
    cy.get('.jumbo-cta .row').within(() => {
      cy.contains('Lorem ipsum dolor sit amet, elit.');
    });

    // Check for eyebrow
    cy.get('.badge').contains('Eyebrow');

    // Check if media image is present
    cy.get('.jumbo-cta img').should('have.attr', 'src', './images/card.webp');

    // Check for body text
    cy.contains('Contra legem facit qui id facit quod lex prohibet.');

    // Check for button
    cy.get('.jumbo-cta .btn-primary').contains('Call to action');

    // Check if flex-lg-row-reverse is applied correctly
    cy.get('.jumbo-cta .row').should('have.css', 'flex-direction', 'row-reverse');
  });

  context('Responsive Design Tests', () => {
    ['ipad-2', 'iphone-6+', 'macbook-15'].forEach((size) => {
      it(`should display correctly on ${size} screen`, () => {
        cy.viewport(size);

        cy.get('.jumbo-cta')
          .should('be.visible');

        cy.get('.badge').contains('Eyebrow');
        cy.contains('Lorem ipsum dolor sit amet, elit.').should('be.visible');
        cy.get('.jumbo-cta img')
          .should('have.attr', 'src', './images/card.webp');
        cy.contains('Contra legem facit qui id facit quod lex prohibet.')
          .should('be.visible');
        cy.get('.jumbo-cta .btn-primary')
          .contains('Call to action')
          .should('be.visible');
      });
    });
  });
});
