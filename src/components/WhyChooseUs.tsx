import { Search, BadgeCheck, Lock, RefreshCw } from "lucide-react";

const features = [
  {
    icon: Search,
    title: "Lightning Fast Search",
    description: "Find any part in seconds with our advanced search and filtering system.",
  },
  {
    icon: BadgeCheck,
    title: "Verified Parts",
    description: "Every part is quality checked and comes with authenticity guarantee.",
  },
  {
    icon: Lock,
    title: "Secure Transactions",
    description: "Bank-grade encryption protects all your payment and personal data.",
  },
  {
    icon: RefreshCw,
    title: "Real-Time Updates",
    description: "Live stock levels and pricing ensure you always have accurate information.",
  },
];

const WhyChooseUs = () => {
  return (
    <section className="section-padding relative overflow-hidden">
      {/* Background Pattern */}
      <div className="absolute inset-0 opacity-5">
        <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-primary rounded-full blur-3xl" />
        <div className="absolute bottom-1/4 right-1/4 w-64 h-64 bg-primary rounded-full blur-3xl" />
      </div>

      <div className="container-custom relative z-10">
        <div className="grid lg:grid-cols-2 gap-16 items-center">
          {/* Left Content */}
          <div>
            <span className="inline-block px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-medium mb-4">
              Why Choose Us
            </span>
            <h2 className="font-display text-4xl md:text-5xl mb-6">
              BUILT FOR
              <br />
              <span className="text-gradient">PROFESSIONALS</span>
            </h2>
            <p className="text-muted-foreground text-lg mb-8">
              We understand the demands of the automotive industry. Our platform is designed 
              to give you the speed, reliability, and accuracy you need to keep your business running.
            </p>

            <div className="flex items-center gap-8">
              <div className="text-center">
                <p className="font-display text-4xl text-primary">15+</p>
                <p className="text-sm text-muted-foreground">Years Experience</p>
              </div>
              <div className="w-px h-16 bg-border" />
              <div className="text-center">
                <p className="font-display text-4xl text-primary">500+</p>
                <p className="text-sm text-muted-foreground">Partner Workshops</p>
              </div>
            </div>
          </div>

          {/* Right - Features Grid */}
          <div className="grid sm:grid-cols-2 gap-6">
            {features.map((feature, index) => (
              <div
                key={feature.title}
                className="p-6 rounded-2xl bg-card border border-border hover:border-primary/30 transition-all duration-300 group"
              >
                <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                  <feature.icon size={24} className="text-primary" />
                </div>
                <h3 className="font-semibold text-lg mb-2">{feature.title}</h3>
                <p className="text-muted-foreground text-sm">{feature.description}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default WhyChooseUs;
