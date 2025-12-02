import { settings } from "@/database/index";

export interface PterodactylSettings {
  panelUrl: string;
  panelAppApiKey: string;
}

export const getPterodactylSettings = async (): Promise<PterodactylSettings | null> => {
  const panelUrl = await settings.findOneBy({ key: "panel_url" }).then((s) => s?.value);
  const panelAppApiKey = await settings
    .findOneBy({ key: "panel_app_api_key" })
    .then((s) => s?.getValue());

  if (!panelUrl || !panelAppApiKey) {
    console.error("Panel URL or API Key not configured");
    return null;
  }

  return { panelUrl, panelAppApiKey };
};

export const suspendPterodactylServer = async (
  serverId: number,
  settings: PterodactylSettings,
): Promise<boolean> => {
  try {
    const response = await fetch(
      new URL(`/api/application/servers/${serverId}/suspend`, settings.panelUrl).toString(),
      {
        method: "POST",
        headers: {
          Authorization: `Bearer ${settings.panelAppApiKey}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      },
    );
    return response.status === 204;
  } catch (error) {
    console.error(`Error suspending server ${serverId}:`, error);
    return false;
  }
};

export const unsuspendPterodactylServer = async (
  serverId: number,
  settings: PterodactylSettings,
): Promise<boolean> => {
  try {
    const response = await fetch(
      new URL(`/api/application/servers/${serverId}/unsuspend`, settings.panelUrl).toString(),
      {
        method: "POST",
        headers: {
          Authorization: `Bearer ${settings.panelAppApiKey}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      },
    );
    return response.status === 204;
  } catch (error) {
    console.error(`Error unsuspending server ${serverId}:`, error);
    return false;
  }
};

export const deletePterodactylServer = async (
  serverId: number,
  settings: PterodactylSettings,
): Promise<boolean> => {
  try {
    const response = await fetch(
      new URL(`/api/application/servers/${serverId}`, settings.panelUrl).toString(),
      {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${settings.panelAppApiKey}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      },
    );
    return response.status === 204;
  } catch (error) {
    console.error(`Error deleting server ${serverId}:`, error);
    return false;
  }
};

export interface CreateServerParams {
  name: string;
  description: string;
  userId: number;
  eggId: number;
  dockerImage: string;
  startup: string;
  environment: Record<string, any>;
  limits: {
    memory: number;
    cpu: number;
    disk: number;
    swap: number;
    io: number;
  };
  featureLimits: {
    databases: number;
    backups: number;
    allocations: number;
  };
  allocationId: number;
}

export interface CreateServerResponse {
  id: number;
  identifier: string;
}

export const createPterodactylServer = async (
  params: CreateServerParams,
  settings: PterodactylSettings,
): Promise<CreateServerResponse | null> => {
  try {
    const response = await fetch(
      new URL("/api/application/servers", settings.panelUrl).toString(),
      {
        method: "POST",
        headers: {
          Authorization: `Bearer ${settings.panelAppApiKey}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          name: params.name,
          description: params.description,
          user: params.userId,
          egg: params.eggId,
          docker_image: params.dockerImage,
          startup: params.startup,
          environment: params.environment,
          limits: {
            memory: params.limits.memory,
            cpu: params.limits.cpu,
            disk: params.limits.disk,
            swap: params.limits.swap,
            io: params.limits.io,
          },
          feature_limits: {
            databases: params.featureLimits.databases,
            backups: params.featureLimits.backups,
            allocations: params.featureLimits.allocations,
          },
          allocation: {
            default: params.allocationId,
          },
        }),
      },
    );

    if (response.status !== 201) {
      console.error(`Failed to create server: HTTP ${response.status}`);
      return null;
    }

    const data = await response.json();

    if (!data?.attributes?.id || !data?.attributes?.identifier) {
      console.error("Invalid response from Pterodactyl: missing server ID or identifier");
      return null;
    }

    return {
      id: data.attributes.id,
      identifier: data.attributes.identifier,
    };
  } catch (error) {
    console.error("Error creating server in Pterodactyl:", error);
    return null;
  }
};
