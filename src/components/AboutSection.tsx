import { Target, Award, Users, TrendingUp } from "lucide-react";

const AboutSection = () => {
  return (
    <section id="about" className="section-padding">
      <div className="container-custom">
        <div className="grid lg:grid-cols-2 gap-16 items-center">
          {/* Left - Visual */}
          <div className="relative">
            <div className="aspect-square rounded-3xl bg-gradient-card border border-border overflow-hidden">
              <div className="absolute inset-0 bg-gradient-to-br from-primary/20 via-transparent to-transparent" />
              <div className="absolute inset-0 flex items-center justify-center">
                <div className="text-center p-8">
                  <div className="w-24 h-24 rounded-2xl bg-gradient-primary flex items-center justify-center mx-auto mb-6 shadow-glow animate-pulse-glow">
                    <span className="font-display text-5xl text-primary-foreground">A</span>
                  </div>
                  <h3 className="font-display text-3xl mb-2">ASHEL'S</h3>
                  <p className="text-muted-foreground">AUTOSPARE</p>
                  <div className="mt-8 pt-8 border-t border-border/50">
                    <p className="font-display text-5xl text-primary mb-2">15+</p>
                    <p className="text-sm text-muted-foreground">Years of Excellence</p>
                  </div>
                </div>
              </div>
            </div>
            
            {/* Floating Elements */}
            <div className="absolute -top-6 -right-6 w-32 h-32 rounded-2xl bg-card border border-border p-4 shadow-card animate-float">
              <Award className="text-primary mb-2" size={28} />
              <p className="font-display text-lg">Award</p>
              <p className="text-xs text-muted-foreground">Winning Service</p>
            </div>
          </div>

          {/* Right - Content */}
          <div>
            <span className="inline-block px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-medium mb-4">
              About Us
            </span>
            <h2 className="font-display text-4xl md:text-5xl mb-6">
              DRIVING YOUR
              <br />
              <span className="text-gradient">SUCCESS FORWARD</span>
            </h2>
            <p className="text-muted-foreground text-lg mb-6">
              Since our founding, Ashel's Autospare has been committed to providing the automotive 
              industry with reliable, high-quality spare parts. We've built our reputation on trust, 
              expertise, and an unwavering dedication to customer satisfaction.
            </p>
            <p className="text-muted-foreground mb-8">
              Our state-of-the-art inventory management system ensures you always get the parts you need, 
              when you need them. We partner with leading manufacturers to bring you genuine parts at 
              competitive prices.
            </p>

            {/* Mission Cards */}
            <div className="grid sm:grid-cols-2 gap-4">
              <div className="flex items-start gap-4 p-4 rounded-xl bg-card border border-border">
                <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                  <Target size={20} className="text-primary" />
                </div>
                <div>
                  <h4 className="font-semibold mb-1">Our Mission</h4>
                  <p className="text-sm text-muted-foreground">
                    Empowering automotive businesses with quality parts and technology.
                  </p>
                </div>
              </div>
              <div className="flex items-start gap-4 p-4 rounded-xl bg-card border border-border">
                <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                  <TrendingUp size={20} className="text-primary" />
                </div>
                <div>
                  <h4 className="font-semibold mb-1">Our Growth</h4>
                  <p className="text-sm text-muted-foreground">
                    Expanding nationwide while maintaining local service quality.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default AboutSection;
