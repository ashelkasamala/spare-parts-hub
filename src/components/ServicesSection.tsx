import { Package, Warehouse, Truck, Users, ClipboardList, Settings } from "lucide-react";

const services = [
  {
    icon: Package,
    title: "Spare Parts Sales",
    description: "Wide selection of genuine and aftermarket parts for all vehicle makes and models.",
  },
  {
    icon: Warehouse,
    title: "Inventory Management",
    description: "Real-time stock tracking and automated reorder alerts to never miss a sale.",
  },
  {
    icon: Truck,
    title: "Supplier Tracking",
    description: "Manage multiple suppliers with performance analytics and delivery tracking.",
  },
  {
    icon: ClipboardList,
    title: "Order Fulfillment",
    description: "Streamlined order processing from placement to delivery confirmation.",
  },
  {
    icon: Users,
    title: "Customer Accounts",
    description: "Dedicated customer portals for order history, wishlists, and quick reorders.",
  },
  {
    icon: Settings,
    title: "System Integration",
    description: "Seamless integration with existing workshop and accounting systems.",
  },
];

const ServicesSection = () => {
  return (
    <section id="services" className="section-padding bg-gradient-dark">
      <div className="container-custom">
        {/* Section Header */}
        <div className="text-center max-w-2xl mx-auto mb-16">
          <span className="inline-block px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-medium mb-4">
            Our Services
          </span>
          <h2 className="font-display text-4xl md:text-5xl mb-4">
            COMPLETE AUTO PARTS
            <br />
            <span className="text-gradient">SOLUTIONS</span>
          </h2>
          <p className="text-muted-foreground">
            Everything you need to manage your auto parts business efficiently
          </p>
        </div>

        {/* Services Grid */}
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {services.map((service, index) => (
            <div
              key={service.title}
              className="group p-8 rounded-2xl bg-gradient-card border border-border hover:border-primary/50 hover-lift cursor-pointer"
              style={{ animationDelay: `${index * 0.1}s` }}
            >
              <div className="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary/20 transition-colors">
                <service.icon size={28} className="text-primary" />
              </div>
              <h3 className="font-display text-2xl mb-3 group-hover:text-primary transition-colors">
                {service.title}
              </h3>
              <p className="text-muted-foreground text-sm leading-relaxed">
                {service.description}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default ServicesSection;
