import {
  mdiMonitor,
  mdiViewModule,
  mdiShieldKey,
  mdiShapeOutline,
  mdiViewDashboard, 
  mdiAccountMultiple , 
  mdiKeyChain ,
  mdiOfficeBuilding,         
  mdiTrademark,       
  mdiShieldAccount,    
  mdiClipboardList,       
  mdiLaptop,        
  mdiServer,          
  mdiClipboardFlow,    
  mdiCertificate, 
  mdiDumbbell     
} from "@mdi/js";

export default [
 {
    //href:"/dashboard",
    route:"dashboard",
    label: "Dashboard",
    icon: mdiViewDashboard,
    role: "Admin",
  },  
//example for submenu
  {
    label: "Seguridad",
    icon: mdiShieldKey,
    role: "Admin",
    menu: [
      {
        route:"users.index",
        label: "Usuarios",
        icon: mdiAccountMultiple,
        role: "Admin",
      }, 
      {
        route: "roles.index",
        label: "Roles",
        icon: mdiShieldAccount,
        role: "Admin",
      },
      
      {
        route: "modules.index",
        label: "Módulos",
        icon: mdiViewModule,
        role: "Admin",
      },
      {
        route: "permissions.index",
        label: "Permisos",
        icon: mdiKeyChain,
        role: "Admin",
      }, 
    ],
  },
 {
  route: "gym.index",
  icon: mdiDumbbell ,
  label: "Gymnasios",
  role: "Admin" 
},
{
  route:"membership.index",
  label: "Membresías",
  icon: mdiCertificate,
  role: "Admin",
  },
  {
  route:"user-memberships.index",
  label: "Membresías para usuarios",
  icon: mdiCertificate,
  role: "Admin",
  },

  //View for memberships ->
];